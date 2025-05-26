<?php
/**
 * Test script for profile photo functionality
 * This script helps test and verify profile photo display functionality
 */

require_once 'vendor/autoload.php';

use App\Models\Acc;
use Illuminate\Support\Facades\Storage;

// Initialize Laravel application
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Testing Profile Photo Functionality\n";
echo "=====================================\n\n";

// Test 1: Check storage setup
echo "📁 Checking storage configuration...\n";
$publicDisk = Storage::disk('public');
echo "Public disk path: " . $publicDisk->path('') . "\n";
echo "Profile photos directory exists: " . ($publicDisk->exists('profile_photos') ? '✅ Yes' : '❌ No') . "\n\n";

// Test 2: Check sample accounts
echo "👥 Checking account records...\n";
$accounts = Acc::select('id', 'firstname', 'lastname', 'profile_photo')->limit(3)->get();
echo "Found " . $accounts->count() . " accounts:\n";
foreach ($accounts as $account) {
    echo "- ID: {$account->id}, Name: {$account->firstname} {$account->lastname}, Photo: " . 
         ($account->profile_photo ? $account->profile_photo : 'None') . "\n";
}
echo "\n";

// Test 3: Create sample profile photo path for testing
echo "🖼️ Sample image test...\n";
$sampleImages = ['aziz.jpg', 'kh.jpg', 'os.jpg'];
foreach ($sampleImages as $image) {
    $publicPath = public_path($image);
    echo "- {$image}: " . (file_exists($publicPath) ? '✅ Exists' : '❌ Missing') . "\n";
}
echo "\n";

// Test 4: Storage link status
echo "🔗 Storage link test...\n";
$storageLinkPath = public_path('storage');
echo "Storage link exists: " . (is_link($storageLinkPath) || is_dir($storageLinkPath) ? '✅ Yes' : '❌ No') . "\n";
if (is_link($storageLinkPath)) {
    echo "Link target: " . readlink($storageLinkPath) . "\n";
}
echo "\n";

echo "✅ Test completed!\n";
echo "\n🔧 Recommendations:\n";
echo "1. Make sure 'php artisan storage:link' has been run\n";
echo "2. Upload profile photos through Filament admin panel\n";
echo "3. Photos should be stored in storage/app/public/profile_photos/\n";
echo "4. They should be accessible via /storage/profile_photos/filename.jpg\n";
