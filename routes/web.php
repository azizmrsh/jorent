<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PropertyGridController;
use App\Http\Controllers\PdfController;
use App\Services\ContractPdfService;
use App\Models\Contract1;

// Home route
Route::get('/', function () {
    return view('home', ['title' => 'Home - Property Management Solution']);
})->name('home');

// Property Grid routes
Route::get('property-grid', [PropertyGridController::class, 'index'])->name('property.grid');
Route::post('property-grid/filter', [PropertyGridController::class, 'filter'])->name('property.grid.filter');

// Contracts routes
Route::resource('contracts', ContractController::class);

// PDF routes for contract PDFs
Route::prefix('pdf')->name('pdf.')->group(function () {
    Route::get('/contract/{id}/generate', [PdfController::class, 'generateContractPdf'])->name('generate');
    Route::get('/contract/{id}/view', [PdfController::class, 'viewPdf'])->name('view');
    Route::get('/contract/{id}/download', [PdfController::class, 'downloadPdf'])->name('download');
    Route::post('/contract/{id}/regenerate', [PdfController::class, 'regeneratePdf'])->name('regenerate');
    Route::get('/contract/{id}/quick-generate', [PdfController::class, 'generateQuickPdf'])->name('quick-generate');
    Route::get('/contract/{id}/status', [PdfController::class, 'checkPdfStatus'])->name('status');
});

// Test route for PDF generation
Route::get('/test-pdf', function () {
    try {
        // Get first contract or create a dummy one for testing
        $contract = Contract1::with(['tenant', 'property.address', 'unit'])->first();
        
        if (!$contract) {
            return response()->json([
                'error' => 'No contracts found. Please create a contract first through the admin panel.',
                'suggestion' => 'Go to /admin/contract1s/create to create a test contract'
            ], 404);
        }
        
        $pdfService = new ContractPdfService();
        $pdfPath = $pdfService->generateContractPdf($contract);
        
        if ($pdfPath) {
            $fullPath = public_path($pdfPath);
            $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
            
            return response()->json([
                'success' => true,
                'message' => 'PDF generated successfully!',
                'pdf_path' => $pdfPath,
                'file_size' => $fileSize,
                'download_url' => asset($pdfPath),
                'contract_id' => $contract->id,
                'tenant_name' => $contract->tenant->name ?? 'N/A'
            ]);
        } else {
            return response()->json([
                'error' => 'PDF generation failed. Check Laravel logs for details.'
            ], 500);
        }
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Exception occurred: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
})->name('test.pdf');