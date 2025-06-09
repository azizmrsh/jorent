<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

class TestLanguageSwitch extends Command
{
    protected $signature = 'test:language-switch';
    protected $description = 'Test the FilamentLanguageSwitch configuration';

    public function handle()
    {
        $this->info('Testing FilamentLanguageSwitch Configuration');
        $this->info('==========================================');
        $this->newLine();

        // Test 1: Check if the package class exists
        if (class_exists(LanguageSwitch::class)) {
            $this->info('✓ FilamentLanguageSwitch package is available');
        } else {
            $this->error('✗ FilamentLanguageSwitch package not found');
        }

        // Test 2: Check language directories
        $langPath = resource_path('lang');
        $enPath = $langPath . '/en';
        $arPath = $langPath . '/ar';

        $this->info("\nLanguage directories:");
        $this->info(is_dir($enPath) ? '✓ English directory exists' : '✗ English directory missing');
        $this->info(is_dir($arPath) ? '✓ Arabic directory exists' : '✗ Arabic directory missing');

        // Test 3: Check current configuration
        $this->info("\nCurrent configuration:");
        $this->info('Default locale: ' . config('app.locale'));
        $this->info('Fallback locale: ' . config('app.fallback_locale'));

        // Test 4: Check if language files exist
        $this->info("\nLanguage files:");
        $this->info(file_exists($arPath . '/validation.php') ? '✓ Arabic validation file exists' : '✗ Arabic validation file missing');
        $this->info(file_exists($arPath . '/general.php') ? '✓ Arabic general file exists' : '✗ Arabic general file missing');
        $this->info(file_exists($enPath . '/general.php') ? '✓ English general file exists' : '✗ English general file missing');

        $this->newLine();
        $this->info('==========================================');
        $this->info('Language Switch Configuration Test Complete!');
        $this->info('The language switcher should now be visible in your Filament admin panel.');
        
        return 0;
    }
}
