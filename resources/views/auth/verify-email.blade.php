@extends('layouts.app')

@section('title', 'Email Verification Required')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <!-- Logo -->
            <div class="mx-auto h-20 w-20 bg-blue-600 rounded-full flex items-center justify-center mb-6">
                <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                📧 التحقق من البريد الإلكتروني مطلوب
            </h2>
            <p class="text-gray-600 mb-6">
                تم إرسال رابط التحقق إلى بريدك الإلكتروني. يرجى فحص بريدك والنقر على الرابط للمتابعة.
            </p>
        </div>

        <!-- Success Messages -->
        @if (session('message'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('message') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Verification Card -->
        <div class="bg-white shadow-xl rounded-lg p-8">
            <div class="space-y-6">
                <!-- User Info -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        مرحباً <span class="font-semibold text-gray-900">{{ Auth::user()->name }}</span>
                    </p>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ Auth::user()->email }}
                    </p>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-blue-800 mb-2">خطوات التحقق:</h3>
                    <ol class="text-sm text-blue-700 space-y-1">
                        <li>1. افتح بريدك الإلكتروني</li>
                        <li>2. ابحث عن رسالة التحقق من {{ config('app.name') }}</li>
                        <li>3. انقر على رابط "تأكيد البريد الإلكتروني"</li>
                        <li>4. ستتم إعادة توجيهك إلى لوحة التحكم</li>
                    </ol>
                </div>

                <!-- Resend Button -->
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        إعادة إرسال رابط التحقق
                    </button>
                </form>

                <!-- Alternative Actions -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex justify-between items-center">
                        <a href="{{ route('home') }}" 
                           class="text-sm text-gray-600 hover:text-gray-900 transition duration-200">
                            ← العودة للصفحة الرئيسية
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="text-sm text-red-600 hover:text-red-900 transition duration-200">
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="text-center">
            <p class="text-xs text-gray-500">
                لم تستلم الرسالة؟ تأكد من مجلد البريد العشوائي (Spam) أو انتظر بضع دقائق ثم أعد المحاولة
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto refresh page if email gets verified in another tab
    let checkInterval;
    
    function checkVerificationStatus() {
        fetch('/api/user/verification-status', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.verified) {
                clearInterval(checkInterval);
                window.location.href = '/admin';
            }
        })
        .catch(error => {
            console.log('Verification check failed:', error);
        });
    }
    
    // Check every 10 seconds
    checkInterval = setInterval(checkVerificationStatus, 10000);
    
    // Clear interval when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(checkInterval);
        } else {
            checkInterval = setInterval(checkVerificationStatus, 10000);
        }
    });
});
</script>
@endsection