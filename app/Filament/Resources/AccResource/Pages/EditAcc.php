<?php

namespace App\Filament\Resources\AccResource\Pages;

use App\Filament\Resources\AccResource;
use App\Models\Acc;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditAcc extends EditRecord
{
    protected static string $resource = AccResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function beforeSave(): void
    {
        // التحقق من تكرار البريد الإلكتروني أثناء التعديل
        $email = $this->data['email'] ?? null;
        $currentId = $this->record->id;
        
        if ($email && Acc::where('email', $email)->where('id', '!=', $currentId)->exists()) {
            Notification::make()
                ->title('خطأ في البيانات')
                ->body("البريد الإلكتروني '{$email}' مستخدم مسبقاً. يرجى اختيار بريد آخر.")
                ->danger()
                ->send();
                
            $this->halt();
        }
    }
    
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('تم تحديث الحساب بنجاح')
            ->body('تم حفظ التغييرات بنجاح.');
    }
}
