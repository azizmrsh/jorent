<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * خريطة المجلدات حسب نوع الملف
     */
    private const DIRECTORY_MAP = [
        // الصور الشخصية
        'profile_photo' => 'users',
        'document_photo' => 'users/documents',
        
        // صور العقارات
        'image_path' => 'properties',
        'images' => 'units',
        
        // العقود والتواقيع
        'tenant_signature_path' => 'contracts/signatures',
        'landlord_signature_path' => 'contracts/signatures',
        'witness1_signature_path' => 'contracts/signatures',
        'witness2_signature_path' => 'contracts/signatures',
        'pdf_path' => 'contracts/pdfs',
        
        // ملفات الدفعات
        'receipt_attachment' => 'payments/receipts',
        'payment_proof' => 'payments/proofs',
        
        // ملفات عامة
        'attachment' => 'documents',
        'file' => 'documents'
    ];

    /**
     * أنواع الملفات المسموحة
     */
    private const ALLOWED_TYPES = [
        'images' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        'documents' => ['pdf', 'doc', 'docx', 'txt'],
        'signatures' => ['png', 'jpg', 'jpeg', 'svg'],
        'all' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'txt', 'svg']
    ];    /**
     * رفع ملف واحد
     */
    public function uploadFile(
        UploadedFile $file, 
        string $fieldName, 
        ?string $customDirectory = null,
        bool $useOriginalName = false
    ): string {
        // تحديد المجلد بناءً على نوع الحقل
        $directory = $customDirectory ?? $this->getDirectoryForField($fieldName);
        
        // إنشاء المجلد إذا لم يكن موجوداً
        $this->ensureDirectoryExists($directory);
        
        // تحديد اسم الملف
        $filename = $this->generateFilename($file, $useOriginalName);
        
        // رفع الملف مباشرة إلى public/uploads
        $targetPath = public_path("uploads/{$directory}/{$filename}");
        
        // نقل الملف
        if ($file->move(public_path("uploads/{$directory}"), $filename)) {
            // إرجاع المسار النسبي للحفظ في قاعدة البيانات
            return "uploads/{$directory}/{$filename}";
        }
        
        throw new \Exception("فشل في رفع الملف");
    }

    /**
     * رفع ملفات متعددة
     */
    public function uploadMultipleFiles(
        array $files, 
        string $fieldName, 
        ?string $customDirectory = null
    ): array {
        $uploadedFiles = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadedFiles[] = $this->uploadFile($file, $fieldName, $customDirectory);
            }
        }
        
        return $uploadedFiles;
    }    /**
     * حذف ملف
     */
    public function deleteFile(?string $filePath): bool
    {
        if (empty($filePath)) {
            return true;
        }

        // تنظيف المسار وإزالة أي بادئات إضافية
        $cleanPath = str_replace(['storage/', 'uploads/'], '', $filePath);
        $fullPath = public_path("uploads/{$cleanPath}");
        
        // حذف الملف إذا كان موجوداً
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        
        return true; // الملف غير موجود أصلاً
    }/**
     * تحديد المجلد بناءً على نوع الحقل
     */
    public function getDirectoryForField(string $fieldName): string
    {
        return self::DIRECTORY_MAP[$fieldName] ?? 'documents';
    }

    /**
     * إنشاء المجلد إذا لم يكن موجوداً
     */
    private function ensureDirectoryExists(string $directory): void
    {
        $fullPath = public_path("uploads/{$directory}");
        
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
    }

    /**
     * توليد اسم ملف فريد
     */
    private function generateFilename(UploadedFile $file, bool $useOriginalName = false): string
    {
        if ($useOriginalName) {
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            return Str::slug($name) . '.' . $extension;
        }

        return time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
    }

    /**
     * التحقق من نوع الملف
     */
    public function validateFileType(UploadedFile $file, string $allowedTypes = 'all'): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $allowed = self::ALLOWED_TYPES[$allowedTypes] ?? self::ALLOWED_TYPES['all'];
        
        return in_array($extension, $allowed);
    }

    /**
     * الحصول على الحجم الأقصى المسموح (بالكيلوبايت)
     */
    public function getMaxFileSize(string $type = 'default'): int
    {
        return match($type) {
            'image' => 5120,      // 5MB للصور
            'document' => 10240,  // 10MB للوثائق
            'signature' => 2048,  // 2MB للتواقيع
            default => 5120
        };
    }

    /**
     * إنشاء جميع المجلدات المطلوبة
     */
    public function createAllDirectories(): void
    {
        $directories = array_unique(array_values(self::DIRECTORY_MAP));
        
        foreach ($directories as $directory) {
            $this->ensureDirectoryExists($directory);
        }

        // مجلدات إضافية
        $additionalDirs = [
            'temp',
            'temp/sample_images',
            'backups'
        ];

        foreach ($additionalDirs as $dir) {
            $this->ensureDirectoryExists($dir);
        }
    }

    /**
     * نسخ ملف موجود إلى مجلد جديد
     */
    public function moveFileToCorrectDirectory(string $currentPath, string $fieldName): ?string
    {
        if (empty($currentPath) || !Storage::disk('public')->exists($currentPath)) {
            return null;
        }

        $correctDirectory = $this->getDirectoryForField($fieldName);
        $filename = basename($currentPath);
        $newPath = "{$correctDirectory}/{$filename}";

        // نسخ الملف إلى المجلد الصحيح
        if (Storage::disk('public')->copy($currentPath, $newPath)) {
            // حذف الملف القديم إذا كان في مجلد مختلف
            if (dirname($currentPath) !== $correctDirectory) {
                Storage::disk('public')->delete($currentPath);
            }
            return $newPath;
        }

        return $currentPath; // إرجاع المسار الأصلي في حالة الفشل
    }    /**
     * الحصول على رابط الملف الكامل
     */
    public function getFileUrl(?string $filePath): ?string
    {
        if (empty($filePath)) {
            return null;
        }

        // إذا كان المسار يحتوي على uploads/ بالفعل، استخدمه مباشرة
        if (str_starts_with($filePath, 'uploads/')) {
            return asset($filePath);
        }
        
        // إضافة uploads/ إذا لم يكن موجوداً
        return asset("uploads/{$filePath}");
    }    /**
     * التحقق من وجود الملف
     */
    public function fileExists(?string $filePath): bool
    {
        if (empty($filePath)) {
            return false;
        }

        // تنظيف المسار
        $cleanPath = str_replace(['storage/', 'uploads/'], '', $filePath);
        $fullPath = public_path("uploads/{$cleanPath}");
        
        return file_exists($fullPath);
    }
}
