<?php

namespace App\Filament\Resources\Contract1Resource\Pages;

use App\Filament\Resources\Contract1Resource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Support\Facades\Storage;

class ViewContract1 extends ViewRecord
{
    protected static string $resource = Contract1Resource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('id')
                    ->label('رقم العقد'),

                TextEntry::make('created_at')
                    ->label('تاريخ الإنشاء'),

                // ✅ التوقيع المعروض
                ImageEntry::make('tenant_signature_path')
                    ->label('توقيع المستأجر')
                    ->getStateUsing(fn ($record) => $record->tenant_signature_path ? Storage::url($record->tenant_signature_path) : null),
            ]);
    }
}
