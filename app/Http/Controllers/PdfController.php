<?php

namespace App\Http\Controllers;

use App\Models\Contract1;
use App\Services\ContractPdfService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    protected $contractPdfService;

    public function __construct(ContractPdfService $contractPdfService)
    {
        $this->contractPdfService = $contractPdfService;
    }

    /**
     * Generate PDF for a specific contract
     */
    public function generateContractPdf(Request $request, $contractId)
    {
        try {
            $contract = Contract1::with(['tenant', 'property.address', 'unit'])->findOrFail($contractId);
            
            $pdfPath = $this->contractPdfService->generateContractPdf($contract);
            
            if ($pdfPath) {
                return response()->json([
                    'success' => true,
                    'message' => 'PDF generated successfully!',
                    'pdf_path' => $pdfPath,
                    'download_url' => asset($pdfPath),
                    'view_url' => route('pdf.view', $contract->id)
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('PDF generation failed', [
                'contract_id' => $contractId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View PDF directly in browser
     */
    public function viewPdf($contractId)
    {
        try {
            $contract = Contract1::findOrFail($contractId);
            
            if (!$contract->pdf_path || !file_exists(public_path($contract->pdf_path))) {
                // Generate PDF if it doesn't exist
                $pdfPath = $this->contractPdfService->generateContractPdf($contract);
                if (!$pdfPath) {
                    abort(404, 'PDF not found and generation failed');
                }
            }
            
            $filePath = public_path($contract->pdf_path);
            
            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="contract_' . $contract->id . '.pdf"'
            ]);
            
        } catch (\Exception $e) {
            Log::error('PDF view failed', [
                'contract_id' => $contractId,
                'error' => $e->getMessage()
            ]);
            
            abort(404, 'PDF not found');
        }
    }

    /**
     * Download PDF file
     */
    public function downloadPdf($contractId)
    {
        try {
            $contract = Contract1::findOrFail($contractId);
            
            if (!$contract->pdf_path || !file_exists(public_path($contract->pdf_path))) {
                // Generate PDF if it doesn't exist
                $pdfPath = $this->contractPdfService->generateContractPdf($contract);
                if (!$pdfPath) {
                    abort(404, 'PDF not found and generation failed');
                }
            }
            
            $filePath = public_path($contract->pdf_path);
            $fileName = 'contract_' . $contract->id . '_' . date('Y-m-d') . '.pdf';
            
            return response()->download($filePath, $fileName, [
                'Content-Type' => 'application/pdf'
            ]);
            
        } catch (\Exception $e) {
            Log::error('PDF download failed', [
                'contract_id' => $contractId,
                'error' => $e->getMessage()
            ]);
            
            abort(404, 'PDF not found');
        }
    }

    /**
     * Regenerate PDF for a contract
     */
    public function regeneratePdf($contractId)
    {
        try {
            $contract = Contract1::with(['tenant', 'property.address', 'unit'])->findOrFail($contractId);
            
            $pdfPath = $this->contractPdfService->regenerateContractPdf($contract);
            
            if ($pdfPath) {
                return response()->json([
                    'success' => true,
                    'message' => 'PDF regenerated successfully!',
                    'pdf_path' => $pdfPath,
                    'download_url' => asset($pdfPath)
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to regenerate PDF'
            ], 500);
            
        } catch (\Exception $e) {
            Log::error('PDF regeneration failed', [
                'contract_id' => $contractId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error regenerating PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick PDF generation using barryvdh/laravel-dompdf as fallback
     */
    public function generateQuickPdf(Request $request, $contractId)
    {
        try {
            $contract = Contract1::with(['tenant', 'property.address', 'unit'])->findOrFail($contractId);
            
            // Use barryvdh/laravel-dompdf as fallback
            $pdf = Pdf::loadView('contracts.pdf', ['contract' => $contract])
                     ->setPaper('A4', 'portrait');
            
            $fileName = 'contract_' . $contract->id . '.pdf';
            $filePath = 'contracts/' . $fileName;
            
            // Save PDF directly to public path
            file_put_contents(public_path($filePath), $pdf->output());
            
            // Update contract with PDF path
            $contract->update(['pdf_path' => $filePath]);
            
            return response()->json([
                'success' => true,
                'message' => 'Quick PDF generated successfully!',
                'pdf_path' => $filePath,
                'download_url' => asset($filePath),
                'method' => 'barryvdh/dompdf'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Quick PDF generation failed', [
                'contract_id' => $contractId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating quick PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check PDF status
     */
    public function checkPdfStatus($contractId)
    {
        try {
            $contract = Contract1::findOrFail($contractId);
            
            $exists = $contract->hasPdf();
            $url = $exists ? $contract->pdf_url : null;
            $fileSize = null;
            
            if ($exists) {
                $filePath = public_path($contract->pdf_path);
                $fileSize = file_exists($filePath) ? filesize($filePath) : 0;
            }
            
            return response()->json([
                'contract_id' => $contractId,
                'pdf_exists' => $exists,
                'pdf_path' => $contract->pdf_path,
                'pdf_url' => $url,
                'file_size' => $fileSize,
                'created_at' => $contract->created_at,
                'updated_at' => $contract->updated_at
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contract not found'
            ], 404);
        }
    }
}