<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Acc;

class CleanDuplicateEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accs:clean-duplicate-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean duplicate email addresses in the accs table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Searching for duplicate emails...');
        
        // البحث عن البريد الإلكتروني المكرر
        $duplicateEmails = Acc::select('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email');
            
        if ($duplicateEmails->isEmpty()) {
            $this->info('No duplicate emails found.');
            return;
        }
        
        $this->warn("Found {$duplicateEmails->count()} duplicate email(s):");
        
        foreach ($duplicateEmails as $email) {
            $records = Acc::where('email', $email)->orderBy('id')->get();
            $this->line("Email: {$email}");
            
            foreach ($records as $index => $record) {
                if ($index === 0) {
                    $this->line("  ✅ Keeping: ID {$record->id} - {$record->firstname} {$record->lastname}");
                } else {
                    $this->line("  ❌ Duplicate: ID {$record->id} - {$record->firstname} {$record->lastname}");
                }
            }
        }
        
        if ($this->confirm('Do you want to remove the duplicate records (keeping the first one for each email)?')) {
            $deletedCount = 0;
            
            foreach ($duplicateEmails as $email) {
                $records = Acc::where('email', $email)->orderBy('id')->get();
                
                // احتفظ بالأول واحذف الباقي
                foreach ($records->skip(1) as $record) {
                    $record->delete();
                    $deletedCount++;
                    $this->line("Deleted: ID {$record->id} - {$record->firstname} {$record->lastname}");
                }
            }
            
            $this->info("Successfully deleted {$deletedCount} duplicate records.");
        } else {
            $this->info('Operation cancelled.');
        }
    }
}
