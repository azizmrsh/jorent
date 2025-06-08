<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>عقد إيجار</title>
    <style>
        /* إعدادات mPDF آمنة */
        @page {
            margin: 5mm 5mm 5mm 5mm;
            size: A4;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background: white;
            color: #333;
            line-height: 1.4;
            direction: rtl;
            text-align: right;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        
        .container {
            width: 100%;
            max-width: 190mm;
            margin: 0 auto;
            padding: 5mm;
        }
        
        /* Header بخلفية سوداء */
        .header {
            background: #000000;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .bismillah {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .contract-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .contract-subtitle {
            font-size: 14px;
        }
        
        /* المحتوى الرئيسي */
        .content {
            padding: 10px;
            line-height: 1.6;
        }
        
        .section-title {
            font-size: 16px;
            color: #333;
            margin: 20px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
            page-break-after: avoid;
        }
        
        /* جدول الأطراف */
        .parties-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .parties-table td {
            padding: 15px;
            border: 1px solid #e1e8f0;
            vertical-align: top;
            width: 50%;
        }
        
        .party-title {
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        /* جدول التفاصيل */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        
        .detail-table td {
            padding: 6px;
            border-bottom: 1px dotted #e1e8f0;
        }
        
        .detail-label {
            font-weight: bold;
            color: #4a6583;
            width: 35%;
        }
        
        .detail-value {
            color: #2c3e50;
            width: 65%;
        }
        
        /* قسم العقار */
        .property-section {
            background: #f9f9f9;
            border: 1px solid #e1e1e1;
            padding: 15px;
            margin: 15px 0;
        }
        
        .property-title {
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        /* الشروط */
        .terms-section {
            margin: 20px 0;
            page-break-inside: avoid;
        }
        
        .terms-list {
            margin: 15px 0;
            padding-right: 20px;
        }
        
        .terms-list li {
            margin-bottom: 8px;
            line-height: 1.5;
        }
        
        /* التوقيعات */
        .signatures-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .signatures-table td {
            padding: 20px;
            border: 1px solid #e1e8f0;
            vertical-align: top;
            width: 50%;
            text-align: center;
        }
        
        .signature-title {
            font-weight: bold;
            margin-bottom: 30px;
            color: #333;
        }
        
        .signature-line {
            border-bottom: 1px solid #333;
            height: 2px;
            margin: 20px 0;
        }
        
        /* Footer */
        .footer {
            margin-top: 20px;
            padding: 15px;
            text-align: center;
            font-size: 11px;
            color: #666;
            border-top: 1px solid #e1e8f0;
        }
        
        /* النصوص الديناميكية بلون أزرق */
        .dynamic-text {
            color: #007bff;
            font-weight: bold;
        }
        
        /* فقرات النص */
        .text-paragraph {
            margin: 10px 0;
            line-height: 1.6;
            text-align: justify;
        }
        
        /* قواعد الطباعة */
        @media print {
            .page-break {
                page-break-before: always;
            }
            
            .no-break {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="bismillah">بسم الله الرحمن الرحيم</div>
            <div class="contract-title">عقد إيجار</div>
            <div class="contract-subtitle">RENTAL AGREEMENT CONTRACT</div>
        </div>

        <!-- Content -->
        <div class="content">
            <!-- معلومات الأطراف -->
            <div class="section-title">أطراف العقد</div>
            
            <table class="parties-table">
                <tr>
                    <td>
                        <div class="party-title">الطرف الأول (المؤجر)</div>
                        <table class="detail-table">
                            <tr>
                                <td class="detail-label">الاسم:</td>
                                <td class="detail-value dynamic-text"><?php echo e($contract->landlord_name ?? 'غير محدد'); ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label">الصفة:</td>
                                <td class="detail-value">مالك العقار</td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <div class="party-title">الطرف الثاني (المستأجر)</div>
                        <table class="detail-table">
                            <tr>
                                <td class="detail-label">الاسم:</td>
                                <td class="detail-value dynamic-text"><?php echo e($contract->tenant_name ?? 'غير محدد'); ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label">رقم الهوية:</td>
                                <td class="detail-value dynamic-text"><?php echo e($contract->tenant->national_id ?? 'غير محدد'); ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label">الجوال:</td>
                                <td class="detail-value dynamic-text"><?php echo e($contract->tenant->phone ?? 'غير محدد'); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <!-- معلومات العقار -->
            <div class="section-title">تفاصيل العقار محل الإيجار</div>
            
            <div class="property-section">
                <div class="property-title"><?php echo e($contract->property->name ?? 'اسم العقار غير محدد'); ?></div>
                <table class="detail-table">
                    <tr>
                        <td class="detail-label">نوع العقار:</td>
                        <td class="detail-value dynamic-text"><?php echo e($contract->unit->usage_type ?? 'سكني'); ?></td>
                    </tr>
                    <tr>
                        <td class="detail-label">رقم الوحدة:</td>
                        <td class="detail-value dynamic-text"><?php echo e($contract->unit->name ?? 'غير محدد'); ?></td>
                    </tr>
                    <tr>
                        <td class="detail-label">المساحة:</td>
                        <td class="detail-value dynamic-text"><?php echo e($contract->unit->area ?? 'غير محدد'); ?> متر مربع</td>
                    </tr>
                    <tr>
                        <td class="detail-label">عدد الغرف:</td>
                        <td class="detail-value dynamic-text"><?php echo e($contract->unit->rooms ?? 'غير محدد'); ?></td>
                    </tr>
                    <tr>
                        <td class="detail-label">عدد الحمامات:</td>
                        <td class="detail-value dynamic-text"><?php echo e($contract->unit->bathrooms ?? 'غير محدد'); ?></td>
                    </tr>
                </table>
            </div>

            <!-- شروط الإيجار -->
            <div class="section-title">شروط وأحكام الإيجار</div>
            
            <table class="detail-table">
                <tr>
                    <td class="detail-label">مدة الإيجار:</td>
                    <td class="detail-value">
                        من <span class="dynamic-text"><?php echo e($contract->start_date ?? 'غير محدد'); ?></span> 
                        إلى <span class="dynamic-text"><?php echo e($contract->end_date ?? 'غير محدد'); ?></span>
                    </td>
                </tr>
                <tr>
                    <td class="detail-label">قيمة الإيجار:</td>
                    <td class="detail-value dynamic-text"><?php echo e(number_format($contract->rent_amount ?? 0, 2)); ?> دينار أردني</td>
                </tr>
                <tr>
                    <td class="detail-label">طريقة الدفع:</td>
                    <td class="detail-value dynamic-text"><?php echo e($contract->payment_method ?? 'شهري مقدم'); ?></td>
                </tr>
            </table>

            <div class="terms-section">
                <div class="section-title">الشروط والأحكام العامة</div>
                <ol class="terms-list">
                    <li>يلتزم المستأجر بدفع قيمة الإيجار في المواعيد المحددة دون تأخير.</li>
                    <li>يلتزم المستأجر بالمحافظة على العقار وعدم إتلافه أو تعديله دون موافقة المؤجر.</li>
                    <li>لا يحق للمستأجر تأجير العقار من الباطن دون موافقة كتابية من المؤجر.</li>
                    <li>يحق للمؤجر معاينة العقار بعد إشعار مسبق مدته 24 ساعة.</li>
                    <li>في حالة التأخر عن دفع الإيجار لمدة تزيد عن شهر، يحق للمؤجر فسخ العقد.</li>
                    <li>يلتزم المستأجر بإرجاع العقار بنفس الحالة التي استلمه عليها.</li>
                </ol>
                
                <?php if($contract->terms_and_conditions_extra): ?>
                <div class="text-paragraph">
                    <strong>شروط إضافية:</strong><br>
                    <span class="dynamic-text"><?php echo e($contract->terms_and_conditions_extra); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- التوقيعات -->
            <div class="signatures-section">
                <div class="section-title">التوقيعات</div>
                
                <table class="signatures-table">
                    <tr>
                        <td>
                            <div class="signature-title">توقيع المؤجر</div>
                            <div class="signature-line"></div>
                            <div><?php echo e($contract->landlord_name ?? '...................'); ?></div>
                            <div>التاريخ: ...................</div>
                        </td>
                        <td>
                            <div class="signature-title">توقيع المستأجر</div>
                            <div class="signature-line"></div>
                            <div><?php echo e($contract->tenant_name ?? '...................'); ?></div>
                            <div>التاريخ: ...................</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            تم إنشاء هذا العقد بتاريخ <?php echo e(date('Y-m-d')); ?> - نظام إدارة العقارات
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\Users\mzyz2\Desktop\الجامعة\last\FinalProject\project\jorentV2\resources\views/contracts/pdf_fixed.blade.php ENDPATH**/ ?>