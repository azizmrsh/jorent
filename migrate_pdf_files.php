<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔄 MIGRATING PDF FILES TO PUBLIC DIRECTORY\n";
echo "==========================================\n\n";

try {
    // Source and destination directories
    $sourceDir = storage_path('app/public/contracts');
    $destDir = public_path('contracts');
    
    // Ensure destination directory exists
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
        echo "✅ Created public/contracts directory\n";
    }
    
    // Check if source directory exists
    if (!is_dir($sourceDir)) {
        echo "ℹ️ No files to migrate - storage/app/public/contracts doesn't exist\n";
        exit(0);
    }
    
    // Get all PDF files from storage
    $files = glob($sourceDir . '/*.pdf');
    
    if (empty($files)) {
        echo "ℹ️ No PDF files found in storage/app/public/contracts\n";
        exit(0);
    }
    
    echo "📁 Found " . count($files) . " PDF files to migrate\n\n";
    
    $migrated = 0;
    $failed = 0;
    
    foreach ($files as $file) {
        $filename = basename($file);
        $destFile = $destDir . '/' . $filename;
        
        try {
            if (copy($file, $destFile)) {
                echo "✅ Migrated: {$filename}\n";
                $migrated++;
            } else {
                echo "❌ Failed to copy: {$filename}\n";
                $failed++;
            }
        } catch (\Exception $e) {
            echo "❌ Error copying {$filename}: " . $e->getMessage() . "\n";
            $failed++;
        }
    }
    
    echo "\n📊 MIGRATION SUMMARY:\n";
    echo "✅ Successfully migrated: {$migrated} files\n";
    echo "❌ Failed: {$failed} files\n";
    
    if ($migrated > 0) {
        echo "\n🎯 NEXT STEPS:\n";
        echo "1. Test that PDF files are accessible via direct URLs\n";
        echo "2. Update any existing contract records if needed\n";
        echo "3. Optionally remove old files from storage/app/public/contracts\n";
        
        // Show example URLs
        $exampleFile = basename($files[0]);
        echo "\n🌐 Example URL: " . config('app.url') . "/contracts/{$exampleFile}\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🏁 Migration completed\n";
