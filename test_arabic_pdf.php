<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Omaralalwi\Gpdf\Gpdf;
use Omaralalwi\Gpdf\GpdfConfig;

try {
    echo "🧪 Testing Arabic PDF Generation with Gpdf...\n";
    
    // Test HTML with Arabic text
    $html = '
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>اختبار الخطوط العربية</title>
        <style>
            body {
                font-family: "Tajawal", "NotoNaskhArabic", "Almarai", sans-serif;
                direction: rtl;
                font-size: 16px;
                padding: 20px;
            }
            .test-section {
                margin: 20px 0;
                padding: 10px;
                border: 1px solid #ccc;
            }
        </style>
    </head>
    <body>
        <div class="test-section">
            <h1>اختبار النصوص العربية</h1>
            <p>هذا نص تجريبي للتأكد من عمل الخطوط العربية بشكل صحيح.</p>
            <p>الأرقام: ١٢٣٤٥٦٧٨٩٠</p>
            <p>التاريخ: ١ يونيو ٢٠٢٥</p>
        </div>
        
        <div class="test-section">
            <h2>Tajawal Font Test</h2>
            <p style="font-family: Tajawal;">مرحباً بكم في اختبار خط تجوال</p>
        </div>
        
        <div class="test-section">
            <h2>NotoNaskhArabic Font Test</h2>
            <p style="font-family: NotoNaskhArabic;">مرحباً بكم في اختبار خط نوتو نسخ العربي</p>
        </div>
        
        <div class="test-section">
            <h2>Almarai Font Test</h2>
            <p style="font-family: Almarai;">مرحباً بكم في اختبار خط المرعي</p>
        </div>
    </body>
    </html>';
    
    // Create gpdf instance
    $gpdfConfig = new GpdfConfig(config('gpdf'));
    $gpdf = new Gpdf($gpdfConfig);
    
    // Generate PDF
    echo "📝 Generating PDF with Arabic content...\n";
    $pdfContent = $gpdf->generate($html);
    
    // Save test PDF
    $testFile = storage_path('app/public/contracts/arabic_font_test.pdf');
    file_put_contents($testFile, $pdfContent);
    
    echo "✅ Test PDF generated successfully!\n";
    echo "📁 File saved at: {$testFile}\n";
    echo "📊 File size: " . number_format(strlen($pdfContent)) . " bytes\n";
    
    // Check if file exists
    if (file_exists($testFile)) {
        $actualSize = filesize($testFile);
        echo "🗂️ Actual file size: " . number_format($actualSize) . " bytes\n";
        
        // Show current gpdf configuration
        echo "\n🔧 Current Gpdf Configuration:\n";
        $config = config('gpdf');
        echo "- Font Directory: " . $config['font_dir'] . "\n";
        echo "- Default Font: " . $config['default_font'] . "\n";
        echo "- Font Cache: " . $config['font_cache'] . "\n";
        
    } else {
        echo "❌ File was not created successfully\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🎯 Arabic font test completed\n";
