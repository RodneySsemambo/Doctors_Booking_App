<?php

namespace App\Filament\Admin\Pages;

use App\Models\AdminWithdrawal;
use App\Models\PlatformWallet;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;
use UnitEnum;

class AdminWithdrawalPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected  string $view = 'filament.admin.pages.admin-withdrawal';

    protected static ?string $title = 'Request Withdrawal';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';
    protected static string|UnitEnum|null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 1;

    public $amount = '';
    public $method = 'mobile_money';
    public $provider = 'mtn';
    public $phone = '';
    public $accountName = '';
    public $bankName = '';
    public $accountNumber = '';
    public $paypalEmail = '';
    public $notes = '';
    public $minimumWithdrawal = 20000;
    public $recentWithdrawals = [];
    public $pendingCount = 0;
    public $processingCount = 0;
    public $completedCount = 0;

    public function mount()
    {
        $this->recentWithdrawals = AdminWithdrawal::latest()->limit(3)->get();
        $this->pendingCount = AdminWithdrawal::where('status', 'pending')->count();
        $this->processingCount = AdminWithdrawal::where('status', 'processing')->count();
        $this->completedCount = AdminWithdrawal::where('status', 'completed')->count();
    }

    protected function getWalletBalance(): float
    {
        $wallet = PlatformWallet::getBalance();
        return max(0, $wallet->platform_earnings - $wallet->total_withdrawn - $wallet->pending_withdrawals);
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('amount')
                ->label('Amount (UGX)')
                ->numeric()
                ->minValue($this->minimumWithdrawal)
                ->prefix('UGX')
                ->placeholder('Enter amount (min 20,000)')
                ->required(),

            Select::make('method')
                ->label('Payment Method')
                ->options([
                    'mobile_money' => 'Mobile Money',
                    'bank_transfer' => 'Bank Transfer',
                    'paypal' => 'PayPal',
                ])
                ->required()
                ->reactive(),

            Select::make('provider')
                ->label('Mobile Provider')
                ->options([
                    'mtn' => 'MTN',
                    'airtel' => 'Airtel',
                ])
                ->visible(fn($get) => $get('method') === 'mobile_money'),

            TextInput::make('phone')
                ->label('Phone Number')
                ->tel()
                ->placeholder('2567xxxxxxx')
                ->visible(fn($get) => $get('method') === 'mobile_money'),

            TextInput::make('accountName')
                ->label('Account Name')
                ->visible(fn($get) => in_array($get('method'), ['mobile_money', 'bank_transfer'])),

            TextInput::make('bankName')
                ->label('Bank Name')
                ->visible(fn($get) => $get('method') === 'bank_transfer'),

            TextInput::make('accountNumber')
                ->label('Account Number')
                ->visible(fn($get) => $get('method') === 'bank_transfer'),

            TextInput::make('paypalEmail')
                ->label('PayPal Email')
                ->email()
                ->visible(fn($get) => $get('method') === 'paypal'),

            Textarea::make('notes')
                ->label('Notes (Optional)')
                ->nullable(),
        ];
    }

    public function submit(): void
    {
        $this->validate();

        $balance = $this->getWalletBalance();

        if ($this->amount > $balance) {
            Notification::make()
                ->title('Insufficient Balance')
                ->body('You cannot withdraw more than the available balance.')
                ->danger()
                ->send();
            return;
        }

        $methodDetails = $this->getMethodDetails();

        $withdrawal = AdminWithdrawal::create([
            'withdrawal_number' => AdminWithdrawal::generateWithdrawalNumber(),
            'amount' => $this->amount,
            'fee' => 0,
            'net_amount' => $this->amount,
            'status' => 'pending',
            'method' => $this->method,
            'method_details' => $methodDetails,
            'notes' => $this->notes,
            'requested_at' => now(),
        ]);

        PlatformWallet::recalculate();

        Notification::make()
            ->title('Withdrawal Request Submitted')
            ->body('Your withdrawal request of UGX ' . number_format($this->amount) . ' has been submitted.')
            ->success()
            ->send();

        $this->reset();
    }

    protected function getMethodDetails(): array
    {
        return match ($this->method) {
            'mobile_money' => [
                'provider' => $this->provider,
                'phone' => $this->phone,
                'account_name' => $this->accountName,
            ],
            'bank_transfer' => [
                'bank_name' => $this->bankName,
                'account_number' => $this->accountNumber,
                'account_name' => $this->accountName,
            ],
            'paypal' => [
                'email' => $this->paypalEmail,
            ],
            default => [],
        };
    }
}
