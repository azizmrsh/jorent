<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Omaralalwi\Gpdf\Gpdf;
use Illuminate\Support\Facades\Storage;

class TestArabicPdf extends Command
{
    protected $signature = 'test:arabic-pdf';
    protected $description = 'Test Arabic PDF generation with gpdf';

    public function handle()
    {
        $this->info('🧪 Testing Arabic PDF Generation with gpdf...');
        
        try {
            // Create simple Arabic HTML
            $arabicHtml = '
            <!DOCTYPE html>
            <html lang="ar" dir="rtl">
            <head>
                <meta charset="UTF-8">
                <style>
                    body {
                        font-family: "DejaVu Sans", sans-serif;
                        direction: rtl;
                        text-align: right;
                        font-size: 14px;
                        line-height: 1.8;
                        color: #333;
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
                        color: #1e40af;
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
                    <h3>اختبار النص العربي:</h3>
                    <p>هذا نص تجريبي باللغة العربية لاختبار قدرة النظام على عرض النص العربي بشكل صحيح.</p>
                </div>
                
                <div class="test-section">
                    <h3>اختبار النصوص الملونة:</h3>
                    <p>المؤجر: <span class="dynamic-field">أحمد محمد علي</span></p>
                    <p>المستأجر: <span class="dynamic-field">فاطمة خالد الزهراني</span></p>
                    <p>مبلغ الإيجار: <span class="dynamic-field">1,500.00 دينار أردني</span></p>
                </div>
                
                <div style="margin-top: 30px; text-align: center;">
                    <p style="font-size: 12px; color: #6b7280;">
                        تم إنشاء هذا الملف التجريبي بواسطة نظام jhome ✅
                    </p>
                </div>
            </body>
            </html>';

            $this->info('1️⃣ Creating gpdf instance...');
            $gpdf = new Gpdf(config('gpdf'));
            $this->info('✅ gpdf instance created successfully');

            $this->info('2️⃣ Generating PDF from Arabic HTML...');
            $pdfContent = $gpdf->generate($arabicHtml);
            $this->info('✅ PDF content generated successfully');

            $this->info('3️⃣ Saving test PDF to storage...');
            $testFile = 'test_arabic_pdf_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Ensure contracts directory exists
            if (!Storage::disk('public')->exists('contracts')) {
                Storage::disk('public')->makeDirectory('contracts');
            }
            
            // Save the PDF
            Storage::disk('public')->put('contracts/' . $testFile, $pdfContent);
            $this->info('✅ Test PDF saved successfully');

            $this->info('4️⃣ Verifying file...');
            if (Storage::disk('public')->exists('contracts/' . $testFile)) {
                $fileSize = Storage::disk('public')->size('contracts/' . $testFile);
                $this->info("✅ File exists and is {$fileSize} bytes");
                
                if ($fileSize > 1000) {
                    $this->info('🎉 SUCCESS: Arabic PDF generation test PASSED!');
                    $this->info('📄 Test file: storage/app/public/contracts/' . $testFile);
                    $this->info('🌐 URL: ' . asset('storage/contracts/' . $testFile));
                    
                    $this->newLine();
                    $this->info('📋 Next Steps:');
                    $this->line('1. Open the generated PDF file to verify Arabic text rendering');
                    $this->line('2. Check that text flows right-to-left correctly');
                    $this->line('3. Verify that blue-colored dynamic fields appear correctly');
                    $this->line('4. Test with actual contract data in Filament');
                } else {
                    $this->warn('⚠️ PDF file size is very small, generation might have failed');
                }
            } else {
                $this->error('❌ PDF file was not created');
            }

        } catch (\Exception $e) {
            $this->error('❌ ERROR: ' . $e->getMessage());
            $this->error('📍 File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            
            $this->newLine();
            $this->info('🔧 Troubleshooting:');
            $this->line('1. Check if gpdf configuration is correct');
            $this->line('2. Verify composer autoload is working');
            $this->line('3. Check if storage directory is writable');
            $this->line('4. Ensure gpdf fonts are properly installed');
        }

        $this->newLine();
        $this->info(str_repeat("=", 50));
        $this->info("Arabic PDF Test Complete");
        $this->info(str_repeat("=", 50));
        
        return Command::SUCCESS;
    }
}
