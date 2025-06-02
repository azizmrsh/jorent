<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OptimizeDbConnections
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // تحسين إعدادات الاتصال
            DB::statement('SET SESSION wait_timeout = 60');
            DB::statement('SET SESSION interactive_timeout = 60');
            
            $response = $next($request);
            
            // إغلاق الاتصالات غير المستخدمة
            DB::disconnect();
            
            return $response;
            
        } catch (\Exception $e) {
            // في حالة فشل الاتصال، لا نريد إيقاف التطبيق
            return $next($request);
        }
    }
    
    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        try {
            // التأكد من إغلاق جميع الاتصالات
            DB::disconnect();
        } catch (\Exception $e) {
            // تجاهل الأخطاء في terminate
        }
    }
}
