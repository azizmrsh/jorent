# حل مشكلة رفع الصور - Internal Server Error

## المشكلة:
```
file_put_contents(/storage/framework/cache/facade-xxx.php): Failed to open stream: No such file or directory
```

## السبب:
- مجلدات التخزين غير موجودة على الخادم
- صلاحيات الملفات غير صحيحة
- مشكلة في الـ cache

## الحل الفوري:

### 1. ارفع ملف `fix_storage_permissions.php` إلى جذر موقعك على الخادم

### 2. شغل الملف من خلال المتصفح:
```
https://jorent.eva-adam.com/fix_storage_permissions.php
```

### 3. أو شغله من cPanel Terminal:
```bash
cd /home/u481455041/domains/jorent.eva-adam.com/public_html
php fix_storage_permissions.php
```

### 4. إصلاح الصلاحيات يدوياً (من cPanel File Manager):
```
storage/ → 755
storage/app/ → 755
storage/app/public/ → 755
storage/app/public/profile_photos/ → 755
storage/framework/ → 755
storage/framework/cache/ → 755
storage/framework/sessions/ → 755
storage/framework/views/ → 755
storage/logs/ → 755
bootstrap/cache/ → 755
public/storage/ → 755
```

### 5. تشغيل أوامر Laravel (من Terminal أو Artisan):
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan storage:link
php artisan config:cache
```

### 6. إنشاء المجلدات المفقودة يدوياً:
إذا لم تكن موجودة، أنشئ هذه المجلدات:
```
/storage/app/public/profile_photos/
/storage/framework/cache/data/
/storage/framework/sessions/
/storage/framework/views/
/storage/logs/
/bootstrap/cache/
```

## التحقق من نجاح الحل:
1. جرب رفع صورة في صفحة الملف الشخصي
2. تأكد من عدم ظهور رسائل خطأ
3. تأكد من حفظ الصورة في المجلد الصحيح

## ملاحظات مهمة:
- تأكد من أن جميع المجلدات لديها صلاحية 755
- تأكد من وجود symlink بين public/storage و storage/app/public
- امسح الـ cache بعد أي تغيير في الإعدادات

## في حالة استمرار المشكلة:
1. تواصل مع الدعم الفني للاستضافة
2. تأكد من إعدادات PHP (memory_limit, upload_max_filesize)
3. تحقق من سجلات الأخطاء في cPanel
