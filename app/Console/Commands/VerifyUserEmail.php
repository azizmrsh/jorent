<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class VerifyUserEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:verify-email {email} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually verify a user\'s email address';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            $unverifiedCount = User::whereNull('email_verified_at')->count();
            
            if ($unverifiedCount === 0) {
                $this->info('All users are already verified!');
                return;
            }
            
            if ($this->confirm("Are you sure you want to verify {$unverifiedCount} unverified users?")) {
                User::whereNull('email_verified_at')->update(['email_verified_at' => now()]);
                $this->info("Successfully verified {$unverifiedCount} users!");
            }
            return;
        }

        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found!");
            return;
        }

        if ($user->hasVerifiedEmail()) {
            $this->info("User '{$email}' is already verified!");
            return;
        }

        $user->update(['email_verified_at' => now()]);
        $this->info("Successfully verified email for user: {$user->name} ({$email})");
    }
}
