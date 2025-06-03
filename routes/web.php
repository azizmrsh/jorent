<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PropertyGridController;
use App\Services\ContractPdfService;
use App\Models\Contract1;
use Illuminate\Support\Facades\Auth;

// Authentication Routes
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

// Home route
Route::get('/', function () {
    return view('home', ['title' => 'Home - Property Management Solution']);
})->name('home');

// Property Grid routes
Route::get('property-grid', [PropertyGridController::class, 'index'])->name('property.grid');
Route::post('property-grid/filter', [PropertyGridController::class, 'filter'])->name('property.grid.filter');

// Contracts routes
Route::resource('contracts', ContractController::class);

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

// Email Verification Routes
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    
    return redirect('/admin')->with('verified', true);
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// API Route للتحقق من حالة التحقق (للاستخدام مع JavaScript)
Route::get('/api/user/verification-status', function (Request $request) {
    return response()->json([
        'verified' => $request->user()->hasVerifiedEmail()
    ]);
})->middleware('auth');

// Tenant Email Verification Routes
use App\Models\Tenant;
use Illuminate\Support\Facades\URL;

Route::get('/tenant/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $tenant = Tenant::findOrFail($id);
    
    if (! hash_equals((string) $hash, sha1($tenant->getEmailForVerification()))) {
        abort(403);
    }
    
    if ($tenant->hasVerifiedEmail()) {
        return redirect('/admin/tenants')->with('message', 'Email already verified!');
    }
    
    if ($tenant->markEmailAsVerified()) {
        return redirect('/admin/tenants')->with('message', 'Email verified successfully!');
    }
    
    return redirect('/admin/tenants')->with('error', 'Email verification failed!');
})->name('tenant.verification.verify')->middleware('signed');

// API Route for tenant verification status
Route::get('/api/tenant/{id}/verification-status', function ($id) {
    $tenant = Tenant::findOrFail($id);
    return response()->json([
        'verified' => $tenant->hasVerifiedEmail(),
        'email' => $tenant->email,
        'verified_at' => $tenant->email_verified_at
    ]);
});

// Test Email Route - إزالة هذا بعد التأكد من عمل البريد
Route::get('/test-email', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('🧪 اختبار البريد الإلكتروني من JoRent', function ($message) {
            $message->to('osaidalhajj03@gmail.com')
                    ->subject('🧪 اختبار البريد - JoRent');
        });
        
        return response()->json([
            'success' => true,
            'message' => '✅ تم إرسال الإيميل بنجاح! تحقق من صندوق الوارد.',
            'config' => [
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'from' => config('mail.from.address')
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'suggestions' => [
                '1. تأكد من أن App Password صحيح',
                '2. تأكد من تفعيل التحقق بخطوتين في Gmail',
                '3. جرب إنشاء App Password جديد'
            ]
        ], 500);
    }
})->name('test.email');