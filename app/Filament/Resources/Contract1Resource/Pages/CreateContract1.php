<?php

namespace App\Filament\Resources\Contract1Resource\Pages;

use App\Filament\Resources\Contract1Resource;
use App\Services\ContractPdfService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class CreateContract1 extends CreateRecord
{
    protected static string $resource = Contract1Resource::class;
    
    /**
     * Called after the record is created
     */
    protected function afterCreate(): void
    {
        // Auto-generate PDF after contract creation
        $this->generateContractPdf();
    }
    
    /**
     * Generate PDF for the created contract
     */
    private function generateContractPdf(): void
    {
        try {
            $contractPdfService = new ContractPdfService();
            $pdfPath = $contractPdfService->generateContractPdf($this->record);
            
            if ($pdfPath) {
                // Show success notification
                Notification::make()
                    ->title('Contract Created Successfully')
                    ->body('Contract PDF has been automatically generated and saved.')
                    ->success()
                    ->duration(5000)
                    ->send();
            } else {
                // Show warning notification if PDF generation failed
                Notification::make()
                    ->title('Contract Created')
                    ->body('Contract was created but PDF generation failed. You can regenerate it later.')
                    ->warning()
                    ->duration(7000)
                    ->send();
            }
            
        } catch (\Exception $e) {
            // Log error and show notification
            Log::error('Contract PDF generation failed during creation', [
                'contract_id' => $this->record->id,
                'error' => $e->getMessage()
            ]);
            
            Notification::make()
                ->title('Contract Created')
                ->body('Contract was created but PDF generation encountered an error.')
                ->warning()
                ->duration(7000)
                ->send();
        }
    }
}
