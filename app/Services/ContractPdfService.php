<?php

namespace App\Services;

use App\Models\Contract1;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class ContractPdfService
{
    /**
     * Generate PDF for a contract and save it to public directory
     *
     * @param Contract1 $contract
     * @return string|null The PDF file path or null if failed
     */
    public function generateContractPdf(Contract1 $contract): ?string
    {
        try {
            // Load relationships
            $contract->load(['tenant', 'property.address', 'unit']);
            
            // Render the view to HTML first
            $html = view('contracts.pdf', ['contract' => $contract])->render();
            
            // Create mPDF instance for Arabic PDF generation with proper configuration
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 20,
                'margin_right' => 20,
                'margin_top' => 25,
                'margin_bottom' => 25,
                'autoArabic' => true,
                'autoLangToFont' => true,
                'autoScriptToLang' => true,
                'default_font' => 'dejavusans',
                'ignore_invalid_utf8' => true,
                'useAdobeCJK' => true,
                'debug' => false,
                'allow_charset_conversion' => true,
                'charset_in' => 'utf-8'
            ]);
            
            // Set default font for better Arabic support
            $mpdf->SetDefaultFont('dejavusans');
            
            // Generate PDF with Arabic support using mPDF
            $mpdf->WriteHTML($html);
            $pdfContent = $mpdf->Output('', 'S');
            
            // Generate filename
            $filename = $this->generateFilename($contract);
            $filepath = "uploads/contracts/{$filename}";
            
            // Ensure the contracts directory exists in public/uploads
            $publicContractsDir = public_path('uploads/contracts');
            if (!is_dir($publicContractsDir)) {
                mkdir($publicContractsDir, 0755, true);
            }
            
            // Save PDF directly to public/uploads/contracts directory
            $fullPath = public_path($filepath);
            file_put_contents($fullPath, $pdfContent);
            
            // Update contract with PDF path (relative to public directory)
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
        if (!$contract->pdf_path) {
            return null;
        }
        
        // Check if file exists in public directory
        $fullPath = public_path($contract->pdf_path);
        if (!file_exists($fullPath)) {
            return null;
        }
        
        // Return direct public URL (no storage/ prefix needed)
        return asset($contract->pdf_path);
    }
    
    /**
     * Delete the PDF file for a contract
     *
     * @param Contract1 $contract
     * @return bool
     */
    public function deleteContractPdf(Contract1 $contract): bool
    {
        if ($contract->pdf_path) {
            $fullPath = public_path($contract->pdf_path);
            if (file_exists($fullPath)) {
                return unlink($fullPath);
            }
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