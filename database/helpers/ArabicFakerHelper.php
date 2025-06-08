<?php

class ArabicFakerHelper
{
    public static function getArabicFirstNames()
    {
        return [
            // Male names
            'محمد', 'أحمد', 'علي', 'حسن', 'عبدالله', 'يوسف', 'إبراهيم', 'عمر', 'خالد', 'سعد',
            'فيصل', 'عبدالرحمن', 'مصطفى', 'حمد', 'طارق', 'ماجد', 'فهد', 'عبدالعزيز', 'نايف', 'بندر',
            'سلطان', 'محمود', 'حسام', 'وليد', 'سامي', 'باسم', 'زياد', 'نبيل', 'رامي', 'عادل',
            
            // Female names
            'فاطمة', 'عائشة', 'خديجة', 'مريم', 'ليلى', 'سارة', 'نورا', 'هند', 'زينب', 'رقية',
            'أمل', 'سمر', 'ندى', 'رنا', 'دينا', 'منى', 'هالة', 'شيماء', 'إيمان', 'سلمى',
            'نادية', 'وفاء', 'هيفاء', 'جميلة', 'كريمة', 'سعاد', 'نجلاء', 'رانيا', 'سميرة', 'لبنى'
        ];
    }

    public static function getArabicLastNames()
    {
        return [
            'الأحمد', 'المحمد', 'العلي', 'الخالدي', 'الزعبي', 'العجلوني', 'المجالي', 'الطراونة',
            'البطاينة', 'السعودي', 'الحسيني', 'القاضي', 'الشريف', 'الهاشمي', 'الرفاعي', 'الكركي',
            'العمري', 'الصالحي', 'الناصر', 'الشامي', 'القادري', 'البرقاوي', 'الدباس', 'العبادي',
            'الخوري', 'المصري', 'اللبناني', 'الفلسطيني', 'الدوسري', 'القحطاني', 'الغامدي', 'المطيري'
        ];
    }

    public static function getJordanianCities()
    {
        return [
            'عمان', 'الزرقاء', 'إربد', 'العقبة', 'السلط', 'مادبا', 'الكرك', 'معان',
            'الطفيلة', 'جرش', 'عجلون', 'المفرق', 'شويفات', 'صويلح', 'الرصيفة', 'القويسمة',
            'أبو نصير', 'تلاع العلي', 'الجبيهة', 'طبربور', 'ماركا', 'النصر', 'البقعة', 'الهاشمي الشمالي'
        ];
    }

    public static function getJordanianJobs()
    {
        return [
            'مهندس', 'طبيب', 'مدرس', 'محاسب', 'صيدلاني', 'محامي', 'ممرض', 'فني',
            'سائق', 'عامل', 'موظف', 'تاجر', 'مقاول', 'كهربائي', 'سباك', 'نجار',
            'خياط', 'حلاق', 'طباخ', 'حارس أمن', 'منظف', 'بائع', 'مصرفي', 'مترجم',
            'مصور', 'صحفي', 'مذيع', 'فنان', 'موسيقي', 'رياضي', 'مدرب', 'مراقب'
        ];
    }

    public static function getMaritalStatuses()
    {
        return ['أعزب', 'متزوج', 'مطلق', 'أرمل'];
    }    public static function getArabicNotes()
    {
        return [
            'لا يوجد ملاحظات خاصة',
            'ملاحظة مهمة - يرجى المتابعة',
            'تم التأكيد والموافقة',
            'بحاجة إلى مراجعة إضافية',
            'في انتظار الموافقة النهائية',
            'مكتمل بنجاح',
            'قيد المراجعة الداخلية',
            'معلق مؤقتاً',
            'مؤجل لأسبوع واحد',
            'تم التوقيع والاعتماد',
            'يحتاج توثيق إضافي',
            'مراجعة قانونية مطلوبة',
            'في انتظار المستندات',
            'تم الانتهاء من المعاملة',
            'يتطلب موافقة المدير',
            'تم الرفض - سبب فني',
            'معاملة طبيعية',
            'حالة استثنائية',
            'بحاجة لتوضيح إضافي',
            'تم التحديث بنجاح'
        ];
    }

    public static function generateNationalId()
    {
        return '1' . str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
    }

    public static function generateJordanianPhone()
    {
        return '+96279' . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
    }

    public static function getRandomArabicName()
    {
        $firstName = self::getArabicFirstNames()[array_rand(self::getArabicFirstNames())];
        $lastName = self::getArabicLastNames()[array_rand(self::getArabicLastNames())];
        return $firstName . ' ' . $lastName;
    }

    public static function getRandomCity()
    {
        return self::getJordanianCities()[array_rand(self::getJordanianCities())];
    }

    public static function getRandomJob()
    {
        return self::getJordanianJobs()[array_rand(self::getJordanianJobs())];
    }

    public static function getRandomMaritalStatus()
    {
        return self::getMaritalStatuses()[array_rand(self::getMaritalStatuses())];
    }

    public static function getRandomNote()
    {
        return self::getArabicNotes()[array_rand(self::getArabicNotes())];
    }

    public static function getRandomBirthdate()
    {
        $year = rand(1965, 2005);
        $month = rand(1, 12);
        $day = rand(1, 28); // Safe day range for all months
        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    // وظائف جديدة لتحسين تنوع البيانات
    public static function getPaymentNotes()
    {
        return [
            'دفعة شهر يناير',
            'دفعة شهر فبراير', 
            'دفعة شهر مارس',
            'دفعة شهر أبريل',
            'دفعة شهر مايو',
            'دفعة شهر يونيو',
            'دفعة شهر يوليو',
            'دفعة شهر أغسطس',
            'دفعة شهر سبتمبر',
            'دفعة شهر أكتوبر',
            'دفعة شهر نوفمبر',
            'دفعة شهر ديسمبر',
            'دفعة مقدمة',
            'دفعة متأخرة',
            'تسوية حساب',
            'رسوم إدارية',
            'تأمين العقار',
            'رسوم صيانة',
            'فواتير كهرباء',
            'فواتير مياه',
            'رسوم تنظيف',
            'دفعة إضافية',
            'خصم خاص',
            'تعويض أضرار',
            'رسوم تجديد العقد'
        ];
    }

    public static function getBankNames()
    {
        return [
            'البنك الأهلي الأردني',
            'البنك العربي',
            'بنك الإسكان للتجارة والتمويل',
            'البنك الإسلامي الأردني',
            'بنك القاهرة عمان',
            'البنك التجاري الأردني',
            'بنك الاتحاد',
            'البنك الاستثماري',
            'بنك المؤسسة العربية المصرفية',
            'البنك الأردني الكويتي'
        ];
    }

    public static function getRandomPaymentNote()
    {
        return self::getPaymentNotes()[array_rand(self::getPaymentNotes())];
    }

    public static function getRandomBankName()
    {
        return self::getBankNames()[array_rand(self::getBankNames())];
    }

    public static function generateTransferReference()
    {
        return 'TXN-' . date('Y') . '-' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }
}