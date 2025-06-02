# 🚨 تعليمات نشر الإصلاح العاجل - مشكلة array_merge()

## 📅 تاريخ الإصلاح: 1 يونيو 2025

---

## ⚡ الإصلاح العاجل المطلوب على الخادم:

### 🎯 المشكلة:
```
Internal Server Error
TypeError: array_merge(): Argument #2 must be of type array, int given
```

### 🔧 الحل تم تطبيقه محلياً:
1. ✅ إزالة `hydrat/filament-table-layout-toggle` من composer.json
2. ✅ تعطيل TableLayoutTogglePlugin في AdminPanelProvider
3. ✅ إصلاح syntax error في AdminPanelProvider
4. ✅ دفع التعديلات إلى GitHub (branch: test)

---

## 🚀 خطوات النشر على الخادم (jorent.eva-adam.com):

### 1️⃣ **الاتصال بالخادم عبر SSH**
```bash
ssh [username]@jorent.eva-adam.com
```

### 2️⃣ **الانتقال إلى مجلد المشروع**
```bash
cd /path/to/jorent/project
```

### 3️⃣ **سحب التعديلات من GitHub**
```bash
git pull origin test
```

### 4️⃣ **إزالة Package المسبب للمشكلة**
```bash
composer remove hydrat/filament-table-layout-toggle
```

### 5️⃣ **تحديث Dependencies**
```bash
composer install --no-dev --optimize-autoloader
composer dump-autoload
```

### 6️⃣ **تنظيف جميع أنواع Cache**
```bash
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 7️⃣ **إعادة بناء Cache للإنتاج**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 8️⃣ **التحقق من عمل النظام**
```bash
php artisan route:list --path=admin
```

---

## 🔍 الملفات المحدثة:

### 📁 `app/Providers/Filament/AdminPanelProvider.php`
- تم تعليق import للـ TableLayoutTogglePlugin
- تم إصلاح syntax error في method chaining

### 📁 `app/Filament/Resources/AccResource/Pages/ListAccs.php`
- تم تعليق HasToggleableTable trait

### 📁 `composer.json`
- تم حذف `"hydrat/filament-table-layout-toggle": "^2.1"`

---

## ✅ التحقق من نجاح الإصلاح:

1. **زيارة الموقع**: https://jorent.eva-adam.com
2. **عدم ظهور Internal Server Error**
3. **إمكانية الوصول لـ admin panel**: https://jorent.eva-adam.com/admin
4. **تسجيل الدخول**: admin@jorent.com / admin123456

---

## 🚨 في حالة استمرار المشكلة:

1. تحقق من ملف logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. تحقق من أن الـ package تم حذفه:
   ```bash
   ls vendor/ | grep hydrat
   ```

3. إعادة تشغيل خادم الويب:
   ```bash
   sudo systemctl restart nginx
   # أو
   sudo systemctl restart apache2
   ```

---

## 📞 الدعم الفني:
- **المطور**: GitHub Copilot
- **آخر تحديث**: 1 يونيو 2025, 10:30 PM
- **حالة الإصلاح**: 🟢 جاهز للنشر
