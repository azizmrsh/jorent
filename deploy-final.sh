#!/bin/bash

# 🚀 سكريبت النشر النهائي - مشروع Jorent
# تاريخ الإنشاء: 1 يونيو 2025

echo "🔄 بدء عملية النشر النهائي..."

# 1. التأكد من التواجد في مجلد المشروع
cd /path/to/jorent-2

# 2. دفع التعديلات إلى Git
echo "📤 دفع التعديلات إلى Git..."
git add .
git commit -m "🔧 Fix: إزالة TableLayoutToggle package نهائياً وحل مشكلة array_merge()"
git push origin main

echo "✅ تم دفع التعديلات بنجاح!"

# 3. تعليمات النشر على الخادم
echo "📋 تعليمات النشر على الخادم:"
echo "-----------------------------------"
echo "ssh إلى الخادم وتنفيذ:"
echo "git pull origin main"
echo "composer install --no-dev --optimize-autoloader"
echo "php artisan migrate --force"
echo "php artisan optimize:clear"
echo "php artisan config:cache"
echo "php artisan route:cache"
echo "php artisan view:cache"

echo "🎉 المشروع جاهز للنشر!"
echo "🌐 الموقع: jorent.eva-adam.com"
echo "👤 تسجيل الدخول: admin@jorent.com / admin123456"
