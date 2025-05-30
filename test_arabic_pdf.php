<?php
/**
 * Simple test script to verify Arabic PDF generation with gpdf
 */

require_once __DIR__ . '/vendor/autoload.php';

// Set timezone to avoid warnings
date_default_timezone_set('UTC');

// Create basic Laravel application context
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Test Arabic text with gpdf
use Omaralalwi\Gpdf\Gpdf;

echo "🧪 Testing Arabic PDF Generation with gpdf...\n\n";

try {
    // Create a simple Arabic HTML test
    $arabicTestHtml = '
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <style>
            @font-face {
                font-family: "ArabicFont";
                src: url("' . public_path('vendor/gpdf/fonts/NotoSansArabic-Regular.ttf') . '") format("truetype");
                font-weight: normal;
            }
            
            @font-face {
                font-family: "ArabicFont";
                src: url("' . public_path('vendor/gpdf/fonts/NotoSansArabic-Bold.ttf') . '") format("truetype");
                font-weight: bold;
            }
            
            body {
                font-family: "ArabicFont", "NotoSansArabic", "DejaVu Sans", sans-serif;
                direction: rtl;
                text-align: right;
                font-size: 14px;
                line-height: 1.8;
                color: #333;
                background: #fff;
                margin: 20px;
            }
            
            .title {
                font-size: 20px;
                font-weight: bold;
                color: #1e40af;
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #3b82f6;
                padding-bottom: 10px;
            }
            
            .dynamic-field {
                color: #1e40af !important;
                font-weight: 600;
                background-color: rgba(30, 64, 175, 0.05);
                padding: 2px 4px;
                border-radius: 3px;
            }
            
            .test-section {
                margin: 15px 0;
                padding: 10px;
                border: 1px solid #e5e7eb;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="title">بسم الله الرحمن الرحيم</div>
        <div class="title">اختبار توليد ملف PDF بالعربية</div>
        
        <div class="test-section">
            <h3>اختبار النص العربي العادي:</h3>
            <p>هذا نص تجريبي باللغة العربية لاختبار قدرة النظام على عرض النص العربي بشكل صحيح من اليمين إلى اليسار مع الحفاظ على تشكيل الحروف وربطها ببعضها البعض.</p>
        </div>
        
        <div class="test-section">
            <h3>اختبار النصوص الديناميكية الملونة:</h3>
            <p>المؤجر: <span class="dynamic-field">أحمد محمد علي</span></p>
            <p>المستأجر: <span class="dynamic-field">فاطمة خالد الزهراني</span></p>
            <p>مبلغ الإيجار: <span class="dynamic-field">1,500.00 دينار أردني</span></p>
            <p>تاريخ البداية: <span class="dynamic-field">2024/01/01</span></p>
        </div>
        
        <div class="test-section">
            <h3>اختبار الأرقام والتواريخ:</h3>
            <p>رقم العقد: <span class="dynamic-field">0001</span></p>
            <p>المدة: <span class="dynamic-field">1 سنة</span></p>
            <p>تاريخ الإنشاء: <span class="dynamic-field">' . date('Y/m/d H:i') . '</span></p>
        </div>
        
        <div class="test-section">
            <h3>اختبار النصوص المختلطة:</h3>
            <p>العنوان: <span class="dynamic-field">شارع الملك عبدالله الثاني، عمان، الأردن</span></p>
            <p>البريد الإلكتروني: <span class="dynamic-field">test@example.com</span></p>
            <p>رقم الهاتف: <span class="dynamic-field">+962-6-1234567</span></p>
        </div>
        
        <div style="margin-top: 30px; text-align: center; padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <p style="font-size: 12px; color: #6b7280;">
                تم إنشاء هذا الملف التجريبي بواسطة نظام jhome لإدارة العقارات
            </p>
            <p style="font-size: 12px; color: #6b7280;">
                نجح اختبار دعم اللغة العربية في ملفات PDF ✅
            </p>
        </div>
    </body>
    </html>';

    echo "1️⃣ Creating gpdf instance...\n";
    $gpdf = new Gpdf();
    echo "✅ gpdf instance created successfully\n\n";

    echo "2️⃣ Generating PDF from Arabic HTML...\n";
    $pdfContent = $gpdf->generate($arabicTestHtml);
    echo "✅ PDF content generated successfully\n\n";

    echo "3️⃣ Saving test PDF to storage...\n";
    $testFile = 'test_arabic_pdf_' . date('Y-m-d_H-i-s') . '.pdf';
    $testPath = storage_path('app/public/' . $testFile);
    
    // Ensure directory exists
    if (!file_exists(dirname($testPath))) {
        mkdir(dirname($testPath), 0755, true);
    }
    
    file_put_contents($testPath, $pdfContent);
    echo "✅ Test PDF saved to: {$testPath}\n\n";

    echo "4️⃣ Verifying file existence and size...\n";
    if (file_exists($testPath)) {
        $fileSize = filesize($testPath);
        echo "✅ File exists and is {$fileSize} bytes\n\n";
        
        if ($fileSize > 1000) { // Basic size check
            echo "🎉 SUCCESS: Arabic PDF generation test PASSED!\n";
            echo "📄 Test file: storage/app/public/{$testFile}\n";
            echo "🌐 URL: " . asset('storage/' . $testFile) . "\n\n";
            
            echo "📋 Next Steps:\n";
            echo "1. Open the generated PDF file to verify Arabic text rendering\n";
            echo "2. Check that text flows right-to-left correctly\n";
            echo "3. Verify that blue-colored dynamic fields appear correctly\n";
            echo "4. Test with actual contract data in Filament\n\n";
        } else {
            echo "⚠️ WARNING: PDF file size is very small, generation might have failed\n";
        }
    } else {
        echo "❌ ERROR: PDF file was not created\n";
    }

} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "📋 Trace: " . $e->getTraceAsString() . "\n\n";
    
    echo "🔧 Troubleshooting:\n";
    echo "1. Check if gpdf fonts are properly installed\n";
    echo "2. Verify composer autoload is working\n";
    echo "3. Check if storage directory is writable\n";
    echo "4. Ensure gpdf configuration is correct\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Arabic PDF Test Complete\n";
echo str_repeat("=", 50) . "\n";
