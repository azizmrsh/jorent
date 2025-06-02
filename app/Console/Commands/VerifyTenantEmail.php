<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerifyTenantEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:verify-email 
                            {email? : The email address of the tenant to verify}
                            {--all : Verify all tenants with unverified emails}
                            {--send : Send verification email instead of marking as verified}
                            {--list : List all tenants with their verification status}
                            {--stats : Show verification statistics}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify tenant email addresses or send verification emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('stats')) {
            $this->showStats();
            return;
        }

        if ($this->option('list')) {
            $this->listTenants();
            return;
        }

        if ($this->option('all')) {
            $this->verifyAllTenants();
            return;
        }

        $email = $this->argument('email');
        if (!$email) {
            $this->error('Please provide an email address or use --all option');
            return;
        }

        $this->verifyTenantEmail($email);
    }

    /**
     * Verify a specific tenant's email
     */
    private function verifyTenantEmail(string $email)
    {
        $tenant = Tenant::where('email', $email)->first();

        if (!$tenant) {
            $this->error("Tenant with email '{$email}' not found.");
            return;
        }

        if ($this->option('send')) {
            try {
                $tenant->sendEmailVerificationNotification();
                $this->info("Verification email sent to {$tenant->firstname} {$tenant->lastname} ({$email})");
            } catch (\Exception $e) {
                $this->error("Failed to send verification email: " . $e->getMessage());
            }
        } else {
            if ($tenant->hasVerifiedEmail()) {
                $this->info("Email for {$tenant->firstname} {$tenant->lastname} is already verified.");
                return;
            }

            $tenant->markEmailAsVerified();
            $this->info("Email verified for {$tenant->firstname} {$tenant->lastname} ({$email})");
        }
    }

    /**
     * Verify all tenants with unverified emails
     */
    private function verifyAllTenants()
    {
        $query = Tenant::whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNull('email_verified_at');

        $count = $query->count();

        if ($count === 0) {
            $this->info('No tenants found with unverified emails.');
            return;
        }

        if ($this->option('send')) {
            if (!$this->confirm("Send verification emails to {$count} tenants?")) {
                $this->info('Operation cancelled.');
                return;
            }

            $sent = 0;
            $failed = 0;

            $query->chunk(100, function ($tenants) use (&$sent, &$failed) {
                foreach ($tenants as $tenant) {
                    try {
                        $tenant->sendEmailVerificationNotification();
                        $sent++;
                        $this->line("✓ Sent to {$tenant->firstname} {$tenant->lastname} ({$tenant->email})");
                    } catch (\Exception $e) {
                        $failed++;
                        $this->line("✗ Failed to send to {$tenant->email}: " . $e->getMessage());
                    }
                }
            });

            $this->info("Verification emails sent: {$sent}, Failed: {$failed}");
        } else {
            if (!$this->confirm("Mark {$count} tenant emails as verified?")) {
                $this->info('Operation cancelled.');
                return;
            }

            $updated = $query->update([
                'email_verified_at' => now()
            ]);

            $this->info("Marked {$updated} tenant emails as verified.");
        }
    }

    /**
     * List all tenants with their verification status
     */
    private function listTenants()
    {
        $tenants = Tenant::whereNotNull('email')
            ->where('email', '!=', '')
            ->select('id', 'firstname', 'lastname', 'email', 'email_verified_at', 'created_at')
            ->orderBy('email_verified_at', 'asc')
            ->get();

        if ($tenants->isEmpty()) {
            $this->info('No tenants found with email addresses.');
            return;
        }

        $headers = ['ID', 'Name', 'Email', 'Verified', 'Verified At'];
        $rows = [];

        foreach ($tenants as $tenant) {
            $rows[] = [
                $tenant->id,
                $tenant->firstname . ' ' . $tenant->lastname,
                $tenant->email,
                $tenant->hasVerifiedEmail() ? '✅ Yes' : '❌ No',
                $tenant->email_verified_at ? $tenant->email_verified_at->format('Y-m-d H:i:s') : 'Not verified'
            ];
        }

        $this->table($headers, $rows);
    }

    /**
     * Show verification statistics
     */
    private function showStats()
    {
        $totalTenants = Tenant::count();
        $tenantsWithEmail = Tenant::whereNotNull('email')->where('email', '!=', '')->count();
        $verifiedTenants = Tenant::whereNotNull('email_verified_at')->count();
        $unverifiedTenants = $tenantsWithEmail - $verifiedTenants;
        
        $verificationRate = $tenantsWithEmail > 0 
            ? round(($verifiedTenants / $tenantsWithEmail) * 100, 2) 
            : 0;

        $this->info('📊 Tenant Email Verification Statistics');
        $this->line('');
        $this->line("📋 Total Tenants: {$totalTenants}");
        $this->line("📧 Tenants with Email: {$tenantsWithEmail}");
        $this->line("✅ Verified Emails: {$verifiedTenants}");
        $this->line("❌ Unverified Emails: {$unverifiedTenants}");
        $this->line("📈 Verification Rate: {$verificationRate}%");
        
        if ($unverifiedTenants > 0) {
            $this->line('');
            $this->warn("💡 Run 'php artisan tenant:verify-email --all --send' to send verification emails to all unverified tenants");
            $this->warn("💡 Run 'php artisan tenant:verify-email --all' to mark all emails as verified");
        }
    }
}
