<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use App\Models\Doctor;
use App\Models\Specialization;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ChatService
{
    // -------------------------------------------------------------------------
    // Conversation management
    // -------------------------------------------------------------------------

    public function startConversation($user_id, $channel = 'web', $external_user_id = null)
    {
        return ChatbotConversation::create([
            'user_id'          => $user_id,
            'session_id'       => $this->getSessionID(),
            'external_user_id' => $external_user_id,
            'status'           => 'active',
            'channel'          => $channel,
            'started_at'       => Carbon::now(),
        ]);
    }

    public function createConversation($user_id, $sessionID = null, $channel = 'web')
    {
        if ($sessionID) {
            $conversation = ChatbotConversation::where('session_id', $sessionID)
                ->where('status', 'active')
                ->first();
            if ($conversation) return $conversation;
        }

        $conversation = ChatbotConversation::where('user_id', $user_id)
            ->where('channel', $channel)
            ->where('status', 'active')
            ->latest()
            ->first();

        return $conversation ?? $this->startConversation($user_id, $channel);
    }

    // -------------------------------------------------------------------------
    // Core message processing
    // -------------------------------------------------------------------------

    public function processMessage(int $conversation_id, string $userMessageText): array
    {
        DB::beginTransaction();
        try {
            $conversation = ChatbotConversation::findOrFail($conversation_id);

            $userMessage = ChatbotMessage::create([
                'chatbot_conversation_id' => $conversation_id,
                'sender_type'             => 'user',
                'message'                 => $userMessageText,
                'message_type'            => 'text',
            ]);

            $analysis = $this->analyzeMessage($conversation, $userMessageText);

            $userMessage->update([
                'intent'   => $analysis['intent'],
                'entities' => $analysis['entities'],
            ]);

            $botResponse = $this->generateResponse($analysis, $conversation);

            $botMessage = ChatbotMessage::create([
                'chatbot_conversation_id' => $conversation_id,
                'sender_type'             => 'bot',
                'message'                 => $botResponse['message'],
                'message_type'            => $botResponse['type'] ?? 'text',
                'quick_replies'           => $botResponse['quick_replies'] ?? null,
            ]);

            DB::commit();

            return [
                'user_message' => $userMessage->fresh(),
                'bot_message'  => $botMessage->fresh(),
            ];
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function analyzeMessage(ChatbotConversation $conversation, string $rawMessage): array
    {
        $message  = strtolower(trim($rawMessage));
        $entities = [];
        $intent   = 'unknown';

        $matchedSpec = $this->matchSpecialization($message);
        if ($matchedSpec) {
            $entities['specialization'] = $matchedSpec;
        }

        // ── STEP 2: Pull conversation context ─────────────────────────────────
        $context        = $this->getConversationContext($conversation->id);
        $lastBotMessage = $context['lastBotMessage'] ?? '';

        // ── STEP 3: Primary intent detection ──────────────────────────────────
        if (preg_match('/\b(hello|hi|hy|hey|good morning|good afternoon|good evening|greetings)\b/', $message)) {
            $intent = 'greeting';
        } elseif (preg_match('/\b(my appointments|upcoming|scheduled|view appointments|show appointments|appointment list)\b/', $message)) {
            $intent = 'view_appointments';
        } elseif (preg_match('/\b(cancel|reschedule|change)\s+(appointment|booking)\b/', $message)) {
            $intent = 'cancel_appointment';
            if (preg_match('/\b(APT-\d+-[A-Z0-9]+)\b/', $message, $m)) {
                $entities['appointment_number'] = $m[0];
            }
        } elseif (preg_match('/\b(book|schedule|see doctor|consultation|need to see a doctor|want to see doctor|can i make an appointmnet)\b/', $message)) {

            $intent = 'book_appointment';
        } elseif (preg_match('/\b(find|search|looking for|show me|recommend)\s+(doctor|specialist|physician)\b/', $message)) {
            $intent = 'find_doctor';
        } elseif (preg_match('/\b(hospital|nearby hospital|private hospital|government hospital)\b/', $message)) {
            $intent = 'hospital';
        } elseif (preg_match('/\b(symptom|sick|pain|fever|headache|cough|ill|feeling|hurt)\b/', $message)) {
            $intent = 'check_symptoms';
            $symptomKeywords = ['fever', 'headache', 'cough', 'pain', 'nausea', 'fatigue', 'dizzy', 'vomiting', 'diarrhea', 'chest pain', 'shortness of breath'];
            foreach ($symptomKeywords as $symptom) {
                if (stripos($message, $symptom) !== false) {
                    $entities['symptoms'][] = $symptom;
                }
            }
        } elseif (preg_match('/\b(payment|pay|fee|cost|price|how much)\b/', $message)) {
            $intent = 'payment_query';
        } elseif (preg_match('/\b(prescription|medication|medicine|drugs)\b/', $message)) {
            $intent = 'prescription_query';
        } elseif (preg_match('/\b(help|assist|what can you do|support|how does this work)\b/', $message)) {
            $intent = 'help';
        } elseif (preg_match('/\b(bye|goodbye|see you|thanks|thank you|that\'s all|no more)\b/', $message)) {
            $intent = 'goodbye';
        }

        $botAskedForSpec = (
            stripos($lastBotMessage, 'specialist') !== false ||
            stripos($lastBotMessage, 'specialization') !== false ||
            stripos($lastBotMessage, 'type of doctor') !== false ||
            stripos($lastBotMessage, 'looking for') !== false
        );

        $botAskedForBooking = (
            stripos($lastBotMessage, 'book an appointment') !== false ||
            stripos($lastBotMessage, 'book appointment') !== false ||
            stripos($lastBotMessage, 'would you like to book') !== false
        );

        if (!empty($entities['specialization']) && $botAskedForSpec) {
            // User replied to "what specialist?" with a specialization name
            // Determine whether context was booking or finding
            $intent = $this->wasBookingContext($lastBotMessage) ? 'book_appointment' : 'provide_specialization';
        } elseif ($intent === 'unknown') {
            // Pure unknown — try context clues
            if ($botAskedForSpec && !empty($entities['specialization'])) {
                $intent = 'provide_specialization';
            } elseif (stripos($lastBotMessage, 'date') !== false || stripos($lastBotMessage, 'when') !== false) {
                $intent = 'provide_date';
                $entities['date'] = $message;
            } elseif (stripos($lastBotMessage, 'confirm') !== false) {
                if (preg_match('/\b(yes|yeah|sure|ok|okay|confirm|correct)\b/', $message)) {
                    $intent = 'confirm_action';
                } elseif (preg_match('/\b(no|nope|cancel|stop)\b/', $message)) {
                    $intent = 'decline_action';
                }
            } elseif (!empty($entities['specialization'])) {
                // User just typed a specialization with no other context — treat as find_doctor
                $intent = 'provide_specialization';
            }
        }

        // ── STEP 5: Other entity extraction ───────────────────────────────────
        if (preg_match('/\b(today|tomorrow|next week|next month|next year)\b/', $message, $m)) {
            $entities['date'] = $m[0];
        }
        if (preg_match('/\b(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})\b/', $message, $m)) {
            $entities['specific_date'] = $m[0];
        }
        if (preg_match('/\b(\d{1,2}:\d{2}|\d{1,2}\s?(am|pm))\b/i', $message, $m)) {
            $entities['time'] = $m[0];
        }
        if (preg_match('/\b(in|near|around)\s+([a-z\s]+)\b/', $message, $m)) {
            $entities['location'] = trim($m[2]);
        }

        Log::info('ChatService analyzeMessage', [
            'message'  => $message,
            'intent'   => $intent,
            'entities' => $entities,
            'lastBot'  => substr($lastBotMessage, 0, 80),
        ]);

        return ['intent' => $intent, 'entities' => $entities];
    }


    public  function matchSpecialization(string $message): ?string
    {
        $dbSpecs = Specialization::all();

        foreach ($dbSpecs as $spec) {
            $name = strtolower($spec->name);
            if (
                stripos($message, $name) !== false ||
                stripos($message, Str::singular($name)) !== false ||
                stripos($message, Str::plural($name)) !== false
            ) {
                return $spec->name;
            }
        }

        // Hardcoded fallbacks mapped to likely DB equivalents
        $hardcoded = [
            'cardiology'      => 'Cardiology',
            'cardiologist'    => 'Cardiology',
            'pediatrics'      => 'Pediatrics',
            'pediatric'       => 'Pediatrics',
            'pediatrician'    => 'Pediatrics',
            'radiology'       => 'Radiology',
            'radiologist'     => 'Radiology',
            'orthopedic'      => 'Orthopedics',
            'orthopedics'     => 'Orthopedics',
            'dermatology'     => 'Dermatology',
            'dermatologist'   => 'Dermatology',
            'neurology'       => 'Neurology',
            'neurologist'     => 'Neurology',
            'gynecology'      => 'Gynecology',
            'gynecologist'    => 'Gynecology',
            'dentist'         => 'Dentistry',
            'dentistry'       => 'Dentistry',
            'psychiatrist'    => 'Psychiatry',
            'psychiatry'      => 'Psychiatry',
            'ophthalmologist' => 'Ophthalmology',
            'ophthalmology'   => 'Ophthalmology',
            'general physician' => 'General Practice',
            'general practice'  => 'General Practice',
            'gp'              => 'General Practice',
        ];

        foreach ($hardcoded as $keyword => $canonical) {
            if (stripos($message, $keyword) !== false) {
                return $canonical;
            }
        }

        return null;
    }

    /**
     * Deciding if the last bot message was part of a booking flow vs. just finding a doctor.
     */
    protected function wasBookingContext(string $lastBotMessage): bool
    {
        return stripos($lastBotMessage, 'book') !== false ||
            stripos($lastBotMessage, 'appointment') !== false;
    }

    // -------------------------------------------------------------------------
    // Response generation
    // -------------------------------------------------------------------------

    protected function generateResponse(array $analysis, ChatbotConversation $conversation): array
    {
        $intent   = $analysis['intent'];
        $entities = $analysis['entities'];

        switch ($intent) {

            case 'greeting':
                return [
                    'message'       => "Hello! 👋 Welcome to our medical consultation service. I'm here to help you find doctors and book appointments. How can I assist you today?",
                    'type'          => 'quick_reply',
                    'quick_replies' => ['Book an appointment', 'Find a doctor', 'View my appointments', 'Check symptoms'],
                ];

            case 'book_appointment':
                if (!empty($entities['specialization'])) {
                    $doctors = $this->findDoctorsBySpecialization($entities['specialization']);
                    if ($doctors->count() > 0) {
                        $top     = $doctors->first();
                        $message = "Great! I found {$doctors->count()} {$entities['specialization']} specialist(s). Here's our top-rated:\n\n";
                        $message .= "👨‍⚕️ Dr. {$top->first_name}\n";
                        $message .= "⭐ {$top->rating}/5 ({$top->total_reviews} reviews)\n";
                        $message .= "💰 UGX " . number_format($top->consultation_fee) . "\n";
                        $message .= "📅 {$top->years_of_experience} years experience\n\n";
                        $message .= "Would you like to book with Dr. {$top->first_name}?";
                        return [
                            'message'       => $message,
                            'type'          => 'quick_reply',
                            'quick_replies' => [
                                "Book with Dr. {$top->first_name}",
                                'Show more doctors',
                                'Different specialization',
                            ],
                        ];
                    }
                    return [
                        'message'       => "I couldn't find any {$entities['specialization']} specialists right now. Please try a different specialization.",
                        'type'          => 'quick_reply',
                        'quick_replies' => ['Radiology', 'Cardiology', 'Pediatrics', 'Dermatology', 'Other specialist'],
                    ];
                }
                return [
                    'message'       => "I have notified the doctor Please try booking the slot form our services with him",
                ];

            case 'find_doctor':
                if (!empty($entities['specialization'])) {
                    $doctors = $this->findDoctorsBySpecialization($entities['specialization']);
                    if ($doctors->count() > 0) {
                        $top     = $doctors->first();
                        $message = "I found {$doctors->count()} {$entities['specialization']} specialist(s). Here's our top-rated doctor:\n\n";
                        $message .= "👨‍⚕️ Dr. {$top->first_name}\n";
                        $message .= "⭐ {$top->rating}/5 ({$top->total_reviews} reviews)\n";
                        $message .= "💰 UGX " . number_format($top->consultation_fee) . "\n";
                        $message .= "📅 {$top->years_of_experience} years experience\n\n";
                        $message .= "Would you like to book with Dr. {$top->first_name}?";
                        return [
                            'message'       => $message,
                            'type'          => 'quick_reply',
                            'quick_replies' => [
                                "Book with Dr. {$top->first_name}",
                                'Show more doctors',
                                'Different specialization',
                            ],
                        ];
                    }
                    return [
                        'message'       => "I couldn't find any {$entities['specialization']} specialists right now. Try a different specialization?",
                        'type'          => 'quick_reply',
                        'quick_replies' => ['Radiology', 'Cardiology', 'Pediatrics', 'Other'],
                    ];
                }
                return [
                    'message'       => "What type of specialist are you looking for?",
                    'type'          => 'quick_reply',
                    'quick_replies' => ['Radiology', 'Cardiology', 'Pediatrics', 'Dermatology', 'Neurology', 'Dentist', 'Psychiatrist'],
                ];


            case 'check_symptoms':
                $symptomsText = !empty($entities['symptoms'])
                    ? "You mentioned: " . implode(', ', $entities['symptoms']) . ". "
                    : '';
                return [
                    'message'       => $symptomsText . "I understand you're not feeling well. While I can provide general guidance, please consult a healthcare professional for a proper diagnosis. Would you like to book an appointment?",
                    'type'          => 'quick_reply',
                    'quick_replies' => ['Yes, book appointment', 'Find general physician', 'Emergency? Call 999', 'Just checking'],
                ];

            case 'view_appointments':
                if (!Auth::check() || !Auth::user()->patient) {
                    return ['message' => "Please make sure you're logged in with a patient profile to view appointments.", 'type' => 'text'];
                }
                $appointments = Appointment::where('patient_id', Auth::user()->patient->id)
                    ->where('appointment_date', '>=', Carbon::now())
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->with('doctor')
                    ->orderBy('appointment_date')
                    ->limit(10)
                    ->get();
                if ($appointments->count() > 0) {
                    $message = "Here are your upcoming appointments:\n\n";
                    foreach ($appointments as $appt) {
                        $message .= "📅 " . $appt->appointment_date;
                        $message .= "👨‍⚕️ Dr. " . $appt->doctor->first_name . "\n";
                        $message .= "📋 " . ucfirst($appt->status) . "\n\n";
                    }
                    return ['message' => $message, 'type' => 'quick_reply', 'quick_replies' => ['Book new appointment', 'Cancel an appointment']];
                }
                return ['message' => "You have no upcoming appointments. Would you like to book one?", 'type' => 'quick_reply', 'quick_replies' => ['Book appointment', 'Find doctors']];

            case 'cancel_appointment':
                return ['message' => "To cancel an appointment, please provide your appointment number (e.g. APT-20240101-ABC123), or visit the 'My Appointments' section in your dashboard.", 'type' => 'text'];

            case 'payment_query':
                return [
                    'message'       => "We accept:\n\n📱 MTN Mobile Money\n📱 Airtel Money\n💰 Cash (at clinic)\n\nConsultation fees vary by doctor. Would you like help finding a doctor?",
                    'type'          => 'quick_reply',
                    'quick_replies' => ['Find doctor', 'Book appointment'],
                ];

            case 'prescription_query':
                return [
                    'message'       => "You can view your prescriptions in the 'My Prescriptions' section of your dashboard.",
                    'type'          => 'quick_reply',
                    'quick_replies' => ['My appointments', 'Book appointment'],
                ];

            case 'help':
                return [
                    'message'       => "I can help you with:\n\n📅 Booking appointments\n👨‍⚕️ Finding doctors\n📋 Viewing appointments\n💊 Prescriptions\n💳 Payment info\n\nWhat would you like to do?",
                    'type'          => 'quick_reply',
                    'quick_replies' => ['Book appointment', 'Find doctor', 'My appointments', 'Payment info'],
                ];

            case 'goodbye':
                return ['message' => "Thank you! Take care and feel better soon. 🌟 Have a great day!", 'type' => 'text'];

            case 'hospital':
                return ['message' => "I can help you find hospitals near you. Which area are you in?", 'type' => 'quick_reply', 'quick_replies' => ['Kampala', 'Entebbe', 'Jinja', 'Mbarara']];

            case 'confirm_action':
                return ['message' => "Great! How else can I help you?", 'type' => 'quick_reply', 'quick_replies' => ['Book appointment', 'Find doctor', 'My appointments']];

            case 'decline_action':
                return ['message' => "No problem! Anything else I can help with?", 'type' => 'quick_reply', 'quick_replies' => ['Book appointment', 'Find doctor', 'Help']];

            default:
                return [
                    'message'       => "I'm not sure I understand. I can help with:\n\n• Booking appointments\n• Finding doctors\n• Viewing appointments\n• Checking symptoms\n• Payment information\n\nWhat would you like?",
                    'type'          => 'quick_reply',
                    'quick_replies' => ['Book appointment', 'Find doctor', 'My appointments', 'Help'],
                ];
        }
    }


    public function findDoctorsBySpecialization(string $specializationName)
    {
        Log::info('ChatService: findDoctorsBySpecialization called', ['name' => $specializationName]);

        // Get matching IDs from DB
        $specializationIds = Specialization::where('name', 'like', "%{$specializationName}%")
            ->orWhere('name', 'like', "%" . Str::singular($specializationName) . "%")
            ->orWhere('name', 'like', "%" . Str::plural($specializationName) . "%")
            ->pluck('id');

        Log::info('ChatService: specialization IDs found', ['ids' => $specializationIds->toArray()]);

        if ($specializationIds->isEmpty()) {
            Log::warning("ChatService: No specialization row found for: {$specializationName}");
            // Last resort — search text columns on doctor table directly
            return Doctor::where('is_verified', true)
                ->where('is_available', true)
                ->where(function ($q) use ($specializationName) {
                    $q->whereRaw('LOWER(specialty) LIKE ?', ['%' . strtolower($specializationName) . '%'])
                        ->orWhereRaw('LOWER(specialization) LIKE ?', ['%' . strtolower($specializationName) . '%'])
                        ->orWhereRaw('LOWER(department) LIKE ?', ['%' . strtolower($specializationName) . '%']);
                })
                ->orderBy('rating', 'desc')
                ->limit(5)
                ->get();
        }

        // Try 1: belongsTo singular — doctor->specialization
        try {
            $results = Doctor::whereHas('specialization', fn($q) => $q->whereIn('id', $specializationIds))
                ->where('is_verified', true)
                ->where('is_available', true)
                ->orderBy('rating', 'desc')
                ->limit(5)
                ->get();

            if ($results->isNotEmpty()) {
                Log::info('ChatService: found via singular relation', ['count' => $results->count()]);
                return $results;
            }
        } catch (\Exception $e) {
            Log::warning('ChatService: singular relation failed — ' . $e->getMessage());
        }

        // Try 2: belongsToMany plural — doctor->specializations
        try {
            $results = Doctor::whereHas('specializations', fn($q) => $q->whereIn('id', $specializationIds))
                ->where('is_verified', true)
                ->where('is_available', true)
                ->orderBy('rating', 'desc')
                ->limit(5)
                ->get();

            if ($results->isNotEmpty()) {
                Log::info('ChatService: found via plural relation', ['count' => $results->count()]);
                return $results;
            }
        } catch (\Exception $e) {
            Log::warning('ChatService: plural relation failed — ' . $e->getMessage());
        }

        // Try 3: direct foreign key column — specialization_id
        try {
            $results = Doctor::whereIn('specialization_id', $specializationIds)
                ->where('is_verified', true)
                ->where('is_available', true)
                ->orderBy('rating', 'desc')
                ->limit(5)
                ->get();

            if ($results->isNotEmpty()) {
                Log::info('ChatService: found via specialization_id column', ['count' => $results->count()]);
                return $results;
            }
        } catch (\Exception $e) {
            Log::warning('ChatService: specialization_id column failed — ' . $e->getMessage());
        }

        // Try 4: ignore is_verified / is_available in case doctors aren't flagged yet
        try {
            $results = Doctor::whereHas('specialization', fn($q) => $q->whereIn('id', $specializationIds))
                ->orderBy('rating', 'desc')
                ->limit(5)
                ->get();

            if ($results->isNotEmpty()) {
                Log::info('ChatService: found without availability filter', ['count' => $results->count()]);
                return $results;
            }
        } catch (\Exception $e) {
            Log::warning('ChatService: unfiltered search failed — ' . $e->getMessage());
        }

        Log::warning('ChatService: No doctors found for ' . $specializationName . ' after all attempts');
        return collect(); // empty collection
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function getConversationContext($conversation_id, $limit = 10): array
    {
        $messages = ChatbotMessage::where('chatbot_conversation_id', $conversation_id)
            ->latest()->limit($limit)->get()->reverse();
        $lastBotMessage = $messages->where('sender_type', 'bot')->last();
        return [
            'messages'       => $messages,
            'lastBotMessage' => $lastBotMessage?->message ?? '',
        ];
    }

    public function getConversationHistory($conversationId, $limit = 50)
    {
        return ChatbotMessage::where('chatbot_conversation_id', $conversationId)
            ->oldest()->limit($limit)->get()->values();
    }

    public function closeConversation($conversationId)
    {
        $conversation = ChatbotConversation::findOrFail($conversationId);
        $conversation->update(['status' => 'closed', 'closed_at' => Carbon::now()]);
        return $conversation;
    }

    public function getUserConversations($userId, $status = null)
    {
        $query = ChatbotConversation::where('user_id', $userId);
        if ($status) $query->where('status', $status);
        return $query->with(['messages' => fn($q) => $q->latest()->limit(1)])->latest()->paginate(20);
    }

    public function getChatbotStats(): array
    {
        $total = ChatbotConversation::count() ?: 1;
        return [
            'total_conversations'           => ChatbotConversation::count(),
            'active_conversations'          => ChatbotConversation::where('status', 'active')->count(),
            'total_messages'                => ChatbotMessage::count(),
            'avg_messages_per_conversation' => round(ChatbotMessage::count() / $total, 1),
            'top_intents'                   => ChatbotMessage::where('sender_type', 'user')
                ->whereNotNull('intent')
                ->select('intent', DB::raw('count(*) as count'))
                ->groupBy('intent')->orderBy('count', 'desc')->limit(5)->get(),
        ];
    }

    protected function getSessionID(): string
    {
        return 'CHAT-' . date('Ymd') . '-' . strtoupper(Str::random(10));
    }
}
