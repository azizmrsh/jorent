<?php

namespace App\Filament\Resources\AccResource\Pages;

use App\Filament\Resources\AccResource;
use App\Models\Acc;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateAcc extends CreateRecord
{
    protected static string $resource = AccResource::class;
    
    protected function beforeCreate(): void
    {
        // التحقق من تكرار البريد الإلكتروني قبل الحفظ
        $email = $this->data['email'] ?? null;
        
        if ($email && Acc::where('email', $email)->exists()) {
            Notification::make()
                ->title('خطأ في البيانات')
                ->body("البريد الإلكتروني '{$email}' مستخدم مسبقاً. يرجى اختيار بريد آخر.")
                ->danger()
                ->send();
                
            $this->halt();
        }
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('تم إنشاء الحساب بنجاح')
            ->body('تم إضافة الحساب الجديد إلى النظام.');
    }
}
