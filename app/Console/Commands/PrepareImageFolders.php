<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FileUploadService;
use App\Models\User;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Acc;
use App\Models\Unit;
use App\Models\Contract1;

class PrepareImageFolders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'storage:prepare-folders {--migrate : نقل الملفات الموجودة إلى المجلدات الصحيحة}';

    /**
     * The console command description.
     */
    protected $description = 'إنشاء مجلدات التخزين وتنظيم الملفات الموجودة';

    private FileUploadService $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        parent::__construct();
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 بدء تحضير مجلدات التخزين...');

        // إنشاء جميع المجلدات المطلوبة
        $this->createDirectories();

        // نقل الملفات الموجودة إذا تم تحديد الخيار
        if ($this->option('migrate')) {
            $this->migrateExistingFiles();
        }

        // عرض إحصائيات
        $this->showStatistics();

        $this->info('✅ تم الانتهاء من تحضير مجلدات التخزين بنجاح!');
    }

    /**
     * إنشاء جميع المجلدات المطلوبة
     */
    private function createDirectories(): void
    {
        $this->info('📁 إنشاء المجلدات...');

        // إنشاء المجلدات الأساسية
        $this->fileUploadService->createAllDirectories();

        $directories = [
            'uploads/users' => 'صور المستخدمين',
            'uploads/users/documents' => 'وثائق المستخدمين',
            'uploads/properties' => 'صور العقارات',
            'uploads/units' => 'صور الوحدات',
            'uploads/contracts/signatures' => 'التواقيع',
            'uploads/contracts/pdfs' => 'ملفات PDF للعقود',
            'uploads/payments/receipts' => 'إيصالات الدفع',
            'uploads/payments/proofs' => 'إثباتات الدفع',
            'uploads/documents' => 'ملفات عامة',
            'uploads/temp' => 'ملفات مؤقتة',
            'uploads/temp/sample_images' => 'صور تجريبية',
            'uploads/backups' => 'نسخ احتياطية'
        ];

        foreach ($directories as $path => $description) {
            $fullPath = public_path($path);
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
                $this->line("  ✓ تم إنشاء: {$path} ({$description})");
            } else {
                $this->line("  - موجود: {$path} ({$description})");
            }
        }
    }

    /**
     * نقل الملفات الموجودة إلى المجلدات الصحيحة
     */
    private function migrateExistingFiles(): void
    {
        $this->info('🔄 نقل الملفات الموجودة...');

        $migratedCount = 0;

        // نقل ملفات المستخدمين
        $migratedCount += $this->migrateUserFiles();

        // نقل ملفات العقارات
        $migratedCount += $this->migratePropertyFiles();

        // نقل ملفات المستأجرين
        $migratedCount += $this->migrateTenantFiles();

        // نقل ملفات الموظفين
        $migratedCount += $this->migrateAccFiles();

        // نقل ملفات الوحدات
        $migratedCount += $this->migrateUnitFiles();

        // نقل ملفات العقود
        $migratedCount += $this->migrateContractFiles();

        $this->info("✅ تم نقل {$migratedCount} ملف إلى المجلدات الصحيحة");
    }

    /**
     * نقل ملفات المستخدمين
     */
    private function migrateUserFiles(): int
    {
        $count = 0;
        $users = User::whereNotNull('profile_photo')->get();

        foreach ($users as $user) {
            $newPath = $this->fileUploadService->moveFileToCorrectDirectory(
                $user->profile_photo, 
                'profile_photo'
            );
            
            if ($newPath && $newPath !== $user->profile_photo) {
                $user->update(['profile_photo' => $newPath]);
                $count++;
            }
        }

        if ($count > 0) {
            $this->line("  ✓ تم نقل {$count} صورة مستخدم");
        }

        return $count;
    }

    /**
     * نقل ملفات العقارات
     */
    private function migratePropertyFiles(): int
    {
        $count = 0;
        $properties = Property::whereNotNull('image_path')->get();

        foreach ($properties as $property) {
            $newPath = $this->fileUploadService->moveFileToCorrectDirectory(
                $property->image_path, 
                'image_path'
            );
            
            if ($newPath && $newPath !== $property->image_path) {
                $property->update(['image_path' => $newPath]);
                $count++;
            }
        }

        if ($count > 0) {
            $this->line("  ✓ تم نقل {$count} صورة عقار");
        }

        return $count;
    }

    /**
     * نقل ملفات المستأجرين
     */
    private function migrateTenantFiles(): int
    {
        $count = 0;
        $tenants = Tenant::where(function($query) {
            $query->whereNotNull('profile_photo')
                  ->orWhereNotNull('document_photo');
        })->get();

        foreach ($tenants as $tenant) {
            // صورة الملف الشخصي
            if ($tenant->profile_photo) {
                $newPath = $this->fileUploadService->moveFileToCorrectDirectory(
                    $tenant->profile_photo, 
                    'profile_photo'
                );
                
                if ($newPath && $newPath !== $tenant->profile_photo) {
                    $tenant->profile_photo = $newPath;
                    $count++;
                }
            }

            // صورة الوثيقة
            if ($tenant->document_photo) {
                $newPath = $this->fileUploadService->moveFileToCorrectDirectory(
                    $tenant->document_photo, 
                    'document_photo'
                );
                
                if ($newPath && $newPath !== $tenant->document_photo) {
                    $tenant->document_photo = $newPath;
                    $count++;
                }
            }

            $tenant->save();
        }

        if ($count > 0) {
            $this->line("  ✓ تم نقل {$count} ملف مستأجر");
        }

        return $count;
    }

    /**
     * نقل ملفات الموظفين
     */
    private function migrateAccFiles(): int
    {
        $count = 0;
        $accs = Acc::where(function($query) {
            $query->whereNotNull('profile_photo')
                  ->orWhereNotNull('document_photo');
        })->get();

        foreach ($accs as $acc) {
            // صورة الملف الشخصي
            if ($acc->profile_photo) {
                $newPath = $this->fileUploadService->moveFileToCorrectDirectory(
                    $acc->profile_photo, 
                    'profile_photo'
                );
                
                if ($newPath && $newPath !== $acc->profile_photo) {
                    $acc->profile_photo = $newPath;
                    $count++;
                }
            }

            // صورة الوثيقة
            if ($acc->document_photo) {
                $newPath = $this->fileUploadService->moveFileToCorrectDirectory(
                    $acc->document_photo, 
                    'document_photo'
                );
                
                if ($newPath && $newPath !== $acc->document_photo) {
                    $acc->document_photo = $newPath;
                    $count++;
                }
            }

            $acc->save();
        }

        if ($count > 0) {
            $this->line("  ✓ تم نقل {$count} ملف موظف");
        }

        return $count;
    }

    /**
     * نقل ملفات الوحدات
     */
    private function migrateUnitFiles(): int
    {
        $count = 0;
        $units = Unit::whereNotNull('images')->get();

        foreach ($units as $unit) {
            if (is_array($unit->images)) {
                $newImages = [];
                $changed = false;

                foreach ($unit->images as $imagePath) {
                    $newPath = $this->fileUploadService->moveFileToCorrectDirectory(
                        $imagePath, 
                        'images'
                    );
                    
                    if ($newPath !== $imagePath) {
                        $changed = true;
                        $count++;
                    }
                    
                    $newImages[] = $newPath;
                }

                if ($changed) {
                    $unit->update(['images' => $newImages]);
                }
            }
        }

        if ($count > 0) {
            $this->line("  ✓ تم نقل {$count} صورة وحدة");
        }

        return $count;
    }    /**
     * نقل ملفات العقود
     */
    private function migrateContractFiles(): int
    {
        $count = 0;
        $contracts = Contract1::where(function($query) {
            $query->whereNotNull('tenant_signature_path')
                  ->orWhereNotNull('landlord_signature_path')
                  ->orWhereNotNull('pdf_path');
        })->get();

        foreach ($contracts as $contract) {
            $signatureFields = [
                'tenant_signature_path',
                'landlord_signature_path'
            ];

            $changed = false;

            foreach ($signatureFields as $field) {
                if ($contract->$field) {
                    $newPath = $this->fileUploadService->moveFileToCorrectDirectory(
                        $contract->$field, 
                        $field
                    );
                    
                    if ($newPath && $newPath !== $contract->$field) {
                        $contract->$field = $newPath;
                        $changed = true;
                        $count++;
                    }
                }
            }

            // ملف PDF
            if ($contract->pdf_path) {
                $newPath = $this->fileUploadService->moveFileToCorrectDirectory(
                    $contract->pdf_path, 
                    'pdf_path'
                );
                
                if ($newPath && $newPath !== $contract->pdf_path) {
                    $contract->pdf_path = $newPath;
                    $changed = true;
                    $count++;
                }
            }

            if ($changed) {
                $contract->save();
            }
        }

        if ($count > 0) {
            $this->line("  ✓ تم نقل {$count} ملف عقد");
        }

        return $count;
    }

    /**
     * عرض إحصائيات المجلدات
     */
    private function showStatistics(): void
    {
        $this->info('📊 إحصائيات المجلدات:');

        $stats = [
            'uploads/users' => $this->countFilesInDirectory('uploads/users'),
            'uploads/properties' => $this->countFilesInDirectory('uploads/properties'),
            'uploads/units' => $this->countFilesInDirectory('uploads/units'),
            'uploads/contracts' => $this->countFilesInDirectory('uploads/contracts'),
            'uploads/payments' => $this->countFilesInDirectory('uploads/payments'),
            'uploads/documents' => $this->countFilesInDirectory('uploads/documents'),
        ];

        foreach ($stats as $dir => $count) {
            $this->line("  {$dir}: {$count} ملف");
        }
    }

    /**
     * عد الملفات في مجلد
     */
    private function countFilesInDirectory(string $directory): int
    {
        $path = public_path($directory);
        if (!is_dir($path)) {
            return 0;
        }

        $files = glob($path . '/**/*', GLOB_BRACE);
        return count(array_filter($files, 'is_file'));
    }
}