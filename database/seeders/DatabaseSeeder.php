<?php

namespace Database\Seeders;

use App\Models\AdminWithdrawal;
use App\Models\Appointment;
use App\Models\ChatbotConversation;
use App\Models\ChatbotMessage;
use App\Models\Doctor;
use App\Models\Doctor_Scheduling;
use App\Models\DoctorScheduling;
use App\Models\Hospital;
use App\Models\MedicalRecord;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\PlatformWallet;
use App\Models\Prescription;
use App\Models\Review;
use App\Models\Reviews;
use App\Models\Sepcialization;
use App\Models\Specialization;
use App\Models\User;
use App\Models\Withdrawal;
use Database\Factories\ReviewFactory;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use PhpParser\Comment\Doc;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run()
    {
        //Find the user you want to update (by email, id, etc.)
        // $user = User::where('email', 'patient@example.org')->first();

        // if ($user) {
        //  $user->password = Hash::make('admin123');  //<-- your new password
        //$user->save();

        // $this->command->info("Password updated for user: {$user->email}");
        //  } else {
        //  $this->command->warn("User not found!");
        // }


        //User::factory(10)->create();
        //MedicalRecord::factory(10)->create();
        Patient::factory(20)->create();
        //Doctor::factory(10)->create();
        //Hospital::factory(5)->create();
        //Specialization::factory(5)->create();
        //Appointment::factory(20)->create();
        //Payment::factory(20)->create();
        //Prescription::factory(10)->create();
        //Withdrawal::factory(10)->create();
        //AdminWithdrawal::factory(10)->create();
        //PlatformWallet::factory(10)->create();
    }
}
