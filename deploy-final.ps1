# 🚀 سكريبت النشر النهائي - مشروع Jorent (PowerShell)
# تاريخ الإنشاء: 1 يونيو 2025

Write-Host "🔄 بدء عملية النشر النهائي..." -ForegroundColor Green

# 1. التأكد من التواجد في مجلد المشروع
Set-Location "c:\Users\osaidsalah002\Documents\jorent-2"

# 2. دفع التعديلات إلى Git
Write-Host "📤 دفع التعديلات إلى Git..." -ForegroundColor Yellow
git add .
git commit -m "🔧 Fix: إزالة TableLayoutToggle package نهائياً وحل مشكلة array_merge()"
git push origin main

Write-Host "✅ تم دفع التعديلات بنجاح!" -ForegroundColor Green

# 3. عرض تعليمات النشر على الخادم
Write-Host "📋 تعليمات النشر على الخادم:" -ForegroundColor Cyan
Write-Host "-----------------------------------" -ForegroundColor White
Write-Host "ssh إلى الخادم وتنفيذ:" -ForegroundColor Yellow
Write-Host "git pull origin main" -ForegroundColor White
Write-Host "composer install --no-dev --optimize-autoloader" -ForegroundColor White
Write-Host "php artisan migrate --force" -ForegroundColor White
Write-Host "php artisan optimize:clear" -ForegroundColor White
Write-Host "php artisan config:cache" -ForegroundColor White
Write-Host "php artisan route:cache" -ForegroundColor White
Write-Host "php artisan view:cache" -ForegroundColor White

Write-Host "🎉 المشروع جاهز للنشر!" -ForegroundColor Green
Write-Host "🌐 الموقع: jorent.eva-adam.com" -ForegroundColor Cyan
Write-Host "👤 تسجيل الدخول: admin@jorent.com / admin123456" -ForegroundColor Cyan
