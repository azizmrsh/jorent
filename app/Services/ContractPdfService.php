<?php

namespace App\Services;

use App\Models\Contract1;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class ContractPdfService
{
    /**
     * Generate PDF for a contract and save it to storage
     *
     * @param Contract1 $contract
     * @return string|null The PDF file path or null if failed
     */
    public function generateContractPdf(Contract1 $contract): ?string
    {
        try {
            // Load relationships
            $contract->load(['tenant', 'property.address', 'unit']);
            
            // Configure PDF options for Arabic RTL support
            $pdf = Pdf::loadView('contracts.pdf', ['contract' => $contract])
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'DejaVu Sans',
                    'isRemoteEnabled' => true,
                    'isHtml5ParserEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                    'defaultMediaType' => 'print',
                    'dpi' => 96,
                    'fontHeightRatio' => 1.1,
                ]);
            
            // Generate filename
            $filename = $this->generateFilename($contract);
            $filepath = "contracts/{$filename}";
            
            // Ensure the contracts directory exists
            if (!Storage::disk('public')->exists('contracts')) {
                Storage::disk('public')->makeDirectory('contracts');
            }
            
            // Save PDF to storage
            $pdfContent = $pdf->output();
            Storage::disk('public')->put($filepath, $pdfContent);
            
            // Update contract with PDF path
            $contract->update(['pdf_path' => $filepath]);
            
            return $filepath;
            
        } catch (\Exception $e) {
            Log::error('Contract PDF generation failed', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }
    
    /**
     * Generate a unique filename for the contract PDF
     *
     * @param Contract1 $contract
     * @return string
     */
    private function generateFilename(Contract1 $contract): string
    {
        $tenantName = Str::slug($contract->tenant->firstname ?? 'unknown');
        $propertyName = Str::slug($contract->property->name ?? 'property');
        $date = now()->format('Y-m-d');
        $contractId = str_pad($contract->id, 4, '0', STR_PAD_LEFT);
        
        return "contract-{$contractId}-{$tenantName}-{$propertyName}-{$date}.pdf";
    }
    
    /**
     * Get the public URL for a contract PDF
     *
     * @param Contract1 $contract
     * @return string|null
     */
    public function getContractPdfUrl(Contract1 $contract): ?string
    {
        if (!$contract->pdf_path || !Storage::disk('public')->exists($contract->pdf_path)) {
            return null;
        }
        
        return asset('storage/' . $contract->pdf_path);
    }
    
    /**
     * Delete the PDF file for a contract
     *
     * @param Contract1 $contract
     * @return bool
     */
    public function deleteContractPdf(Contract1 $contract): bool
    {
        if ($contract->pdf_path && Storage::disk('public')->exists($contract->pdf_path)) {
            return Storage::disk('public')->delete($contract->pdf_path);
        }
        
        return true;
    }
    
    /**
     * Regenerate PDF for an existing contract
     *
     * @param Contract1 $contract
     * @return string|null
     */
    public function regenerateContractPdf(Contract1 $contract): ?string
    {
        // Delete old PDF if exists
        $this->deleteContractPdf($contract);
        
        // Generate new PDF
        return $this->generateContractPdf($contract);
    }
}