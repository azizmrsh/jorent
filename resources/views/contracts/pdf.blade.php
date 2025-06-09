<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>عقد إيجار</title>
    @php
        use Illuminate\Support\Facades\Storage;
    @endphp
    <style>
        @page {
            margin: 25px 20px;
        }
     <div class="container">
        <div class="header">
            <div style="font-size: 16px; margin-bottom: 10px; font-weight: bold;">بسم الله الرحمن الرحيم</div>
            <h1 class="contract-title">عقد إيجار رسمي</h1>
            <p class="contract-subtitle">محرر بتاريخ {{ \Carbon\Carbon::now()->format('Y/m/d') }}</p>
        </div>  
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DejaVu Sans', 'Amiri', sans-serif;
        }
        
        body {
            background: white;
            color: #333;
            line-height: 1.4;
            direction: rtl;
            text-align: right;
            font-family: 'DejaVu Sans', 'Amiri', sans-serif;
            font-size: 13px;
        }
        
        .container {
            width: 100%;
            background: white;
        }
        
        .header {
            background: #000000 !important;
            color: #ffffff !important;
            padding: 25px !important;
            text-align: center !important;
            margin-bottom: 25px !important;
            border: 4px solid #000000 !important;
            display: block !important;
            width: 100% !important;
            min-height: 120px !important;
            box-sizing: border-box !important;
            position: relative !important;
            z-index: 999 !important;
        }
        
        .bismillah {
            font-size: 18px !important;
            font-weight: bold !important;
            color: #ffffff !important;
            margin-bottom: 15px !important;
            display: block !important;
            text-align: center !important;
            line-height: 1.2 !important;
            text-shadow: none !important;
            background: transparent !important;
        }
        
        .contract-title {
            font-size: 26px !important;
            font-weight: bold !important;
            color: #ffffff !important;
            margin-bottom: 12px !important;
            display: block !important;
            text-align: center !important;
            line-height: 1.2 !important;
            text-shadow: none !important;
            background: transparent !important;
        }
        
        .contract-subtitle {
            font-size: 16px !important;
            color: #ffffff !important;
            display: block !important;
            text-align: center !important;
            line-height: 1.2 !important;
            text-shadow: none !important;
            background: transparent !important;
        }
        
        .content {
            padding: 0 15px;
        }
        
        .section-title {
            font-size: 16px;
            color: #333;
            margin: 15px 0 10px 0;
            padding-bottom: 3px;
            border-bottom: 2px solid #ddd;
            font-weight: bold;
        }
        
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
            font-size: 16px;
            color: #333;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        
        .detail-table td {
            padding: 8px;
            border-bottom: 1px dotted #e1e8f0;
        }
        
        .detail-label {
            font-weight: bold;
            color: #4a6583;
            width: 30%;
        }
        
        .detail-value {
            color: #2c3e50;
            width: 70%;
        }
        
        .property-section {
            background: #f9f9f9;
            border: 1px solid #e1e1e1;
            padding: 20px;
            margin: 15px 0;
        }
        
        .property-title {
            font-size: 16px;
            color: #333;
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .terms-section {
            margin: 15px 0;
        }
        
        .term-item {
            margin-bottom: 8px;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #e1e1e1;
            page-break-inside: avoid;
            display: block;
            overflow: hidden;
            min-height: 40px;
        }
        
        .term-number {
            display: inline-block;
            background: #000000;
            color: #ffffff;
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px;
            font-weight: bold;
            font-size: 11px;
            margin-left: 8px;
            vertical-align: top;
            flex-shrink: 0;
        }
        
        .term-content {
            display: inline-block;
            line-height: 1.6;
            font-size: 12px;
            white-space: pre-wrap;
            word-wrap: break-word;
            word-break: break-word;
            vertical-align: top;
            width: calc(100% - 35px);
            min-height: auto;
        }
        
        .signature-section {
            margin-top: 20px;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #e1e1e1;
            page-break-inside: avoid;
        }
        
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .signature-table td {
            width: 25%;
            text-align: center;
            padding: 10px;
            border: 1px solid #e1e8f0;
        }
        
        .signature-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
            font-size: 13px;
        }
        
        .signature-line {
            height: 2px;
            background: #000000;
            margin: 10px 0;
        }
        
        .signature-name {
            font-size: 11px;
            color: #4a6583;
            margin-top: 5px;
        }
        
        .footer {
            padding: 15px;
            text-align: center;
            background: #f9f9f9;
            color: #666;
            font-size: 11px;
            border-top: 1px solid #ddd;
            margin-top: 15px;
        }
        
        .footer-logo {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }
        
        .contract-number {
            text-align: left;
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        
        .closing-statement {
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="bismillah">بسم الله الرحمن الرحيم</div>
            <div class="contract-title">عقد إيجار رسمي</div>
            <div class="contract-subtitle">محرر بتاريخ {{ \Carbon\Carbon::now()->format('Y/m/d') }}</div>
        </div>
        
        <div class="content">
            <div class="contract-number">رقم العقد: {{ $contract->id ?? 'غير محدد' }}</div>
            
            <h2 class="section-title">أطراف العقد</h2>
            
            <table class="parties-table">
                <tr>
                    <td>
                        <h3 class="party-title">الطرف الأول (المؤجر)</h3>
                        <table class="detail-table">
                            <tr>
                                <td class="detail-label">الاسم:</td>
                                <td class="detail-value">
                                    <span style="color:#1d4ed8;">{{ $contract->landlord_name ?? 'غير محدد' }}</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <h3 class="party-title">الطرف الثاني (المستأجر)</h3>
                        <table class="detail-table">
                            <tr>
                                <td class="detail-label">الاسم:</td>
                                <td class="detail-value">
                                    <span style="color:#1d4ed8;">
                                        @php
                                            $tenantName = '';
                                            if (isset($contract->tenant_name_accessor) && !empty($contract->tenant_name_accessor)) {
                                                $tenantName = $contract->tenant_name_accessor;
                                            } elseif (isset($contract->tenant)) {
                                                $firstName = $contract->tenant->firstname ?? '';
                                                $lastName = $contract->tenant->lastname ?? '';
                                                $tenantName = trim($firstName . ' ' . $lastName);
                                            }
                                            echo !empty($tenantName) ? $tenantName : 'غير محدد';
                                        @endphp
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            
            <h2 class="section-title">تفاصيل المأجور</h2>
            
            <div class="property-section">
                <h3 class="property-title">وصف المأجور</h3>
                <table class="detail-table">
                    <tr>
                        <td class="detail-label">الموقع:</td>
                        <td class="detail-value">
                            <span style="color:#1d4ed8;">{{ $contract->property_name_accessor ?? ($contract->property->name ?? 'غير محدد') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">الوحدة:</td>
                        <td class="detail-value">
                            <span style="color:#1d4ed8;">{{ $contract->unit_name_accessor ?? ($contract->unit->name ?? 'غير محدد') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="detail-label">نوع الاستخدام:</td>
                        <td class="detail-value">
                            <span style="color:#1d4ed8;">{{ $contract->unit->usage_type ?? 'سكني' }}</span>
                        </td>
                    </tr>
                </table>
            </div>
            
            <table class="detail-table">
                <tr>
                    <td class="detail-label">تاريخ ابتداء الإيجار:</td>
                    <td class="detail-value">
                        <span style="color:#1d4ed8;">{{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('Y/m/d') : 'غير محدد' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="detail-label">تاريخ انتهاء الإيجار:</td>
                    <td class="detail-value">
                        <span style="color:#1d4ed8;">{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('Y/m/d') : 'غير محدد' }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="detail-label">مدة الإيجار:</td>
                    <td class="detail-value">
                        <span style="color:#1d4ed8;">
                            @if ($contract->start_date && $contract->end_date)
                                @php
                                    $startDate = \Carbon\Carbon::parse($contract->start_date);
                                    $endDate = \Carbon\Carbon::parse($contract->end_date);
                                    $diff = $startDate->diff($endDate);
                                    $durationParts = [];
                                    if ($diff->y > 0) $durationParts[] = $diff->y . ' ' . ($diff->y == 1 ? 'سنة' : ($diff->y == 2 ? 'سنتين' : $diff->y . ' سنوات'));
                                    if ($diff->m > 0) $durationParts[] = $diff->m . ' ' . ($diff->m == 1 ? 'شهر' : ($diff->m == 2 ? 'شهرين' : $diff->m . ' شهور'));
                                    if ($diff->d > 0 && count($durationParts) < 2) $durationParts[] = $diff->d . ' ' . ($diff->d == 1 ? 'يوم' : ($diff->d == 2 ? 'يومين' : $diff->d . ' أيام'));
                                    echo implode(' و ', $durationParts);
                                @endphp
                            @else
                                غير محددة
                            @endif
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="detail-label">بدل الإيجار السنوي:</td>
                    <td class="detail-value">
                        <span style="color:#1d4ed8;">{{ number_format($contract->rent_amount ?? 0, 2) }} دينار أردني</span>
                    </td>
                </tr>
                <tr>
                    <td class="detail-label">كيفية أداء البدل:</td>
                    <td class="detail-value">
                        <span style="color:#1d4ed8;">{{ $contract->payment_method ?? 'شهري مقدم، يستحق في اليوم الأول من كل شهر ميلادي' }}</span>
                    </td>
                </tr>
            </table>
            
            <h2 class="section-title">شروط وأحكام العقد</h2>
            
            <div class="terms-section">
                <div class="term-item">
                    <span class="term-number">1</span>
                    <span class="term-content">استلم المستأجر المأجور سالماً من كل عيب تام أو خلل ويتعهد بتسليمه عند إنتهاء مدة الإجارة كما استلمه.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">2</span>
                    <span class="term-content">عند إنتهاء مدة الإجارة، على المستأجر أن يأخذ وصلاً خطياً من المؤجر يتضمن استلامه للمأجور وتوابعه سالماً. في حال وجود أي عيب أو تلف، يحق للمؤجر إصلاحه على نفقة المستأجر.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">3</span>
                    <span class="term-content">ليس للمستأجر الحق بتأجير المأجور كلياً أو جزء منه للغير أو المشاركة مع الغير بدون موافقة المؤجر الخطية.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">4</span>
                    <span class="term-content">لا يحق للمستأجر إحداث أي تغيير في المأجور من هدم أو بناء أو فتح شبابيك أو تغيير في الأبواب أو الحنفيات إلا بموافقة المؤجر الخطية.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">5</span>
                    <span class="term-content">عموم ما يحصل في المأجور من عطل أو عيب كخراب في المجاري أو التمديدات فيعود تصليحه على المستأجر ولا يحق له مطالبة المؤجر بأي تعويضات.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">6</span>
                    <span class="term-content">إذا امتنع أو تأخر المستأجر عن دفع أي قسط من الأقساط في ميعاد استحقاقه، تصبح جميع الأقساط الأخرى مستحقة الأداء حالاً، وللمؤجر حق فسخ العقد واستلام المأجور.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">7</span>
                    <span class="term-content">بحال حدوث أي مما ذكر في البندين الخامس والسادس، يحق للمؤجر وضع يده على أموال المستأجر الموجودة في المأجور وبيعها واستيفاء حقوقه من ثمنها.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">8</span>
                    <span class="term-content">للمؤجر الحق في بناء طوابق علوية فوق المأجور أو بالقرب منه وعمل جميع التصليحات التي يريدها دون أن يكون للمستأجر حق بطلب تعويض أو تنزيل أجرة.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">9</span>
                    <span class="term-content">جميع ما يعمله المستأجر من تنظيمات أو تصليحات ووضع بورسلين أو غيره تكون نفقتها عليه وعند خروجه يكون المؤجر مخيراً بين أخذها بدون مقابل أو طلب إعادة المأجور إلى حالته الأصلية على حساب المستأجر.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">10</span>
                    <span class="term-content">لا يجوز للمستأجر أن يشغل المأجور لغير الغاية التي استأجره لأجلها أو أن يستعمله فيما يخالف الشرع والقانون وأنظمة البلاد والآداب العامة.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">11</span>
                    <span class="term-content">في حالة تعدد المستأجرين، يعتبرون متكافلين ومتضامنين في جميع أحكام العقد والتزاماته، وأي تبليغ لأحدهم يعتبر تبليغاً للجميع.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">12</span>
                    <span class="term-content">لا حاجة لتبادل أي إخطار أو إنذار بين الطرفين إلا في الحالات التي نص فيها العقد على ذلك.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">13</span>
                    <span class="term-content">يسقط المستأجر من الآن ادعاء كذب الإقرار في هذا العقد كلياً أو جزئياً وفيما يتفرع عنه من كمبيالات وشيكات ومستندات.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">14</span>
                    <span class="term-content">ثمن المياه والكهرباء وجميع الضرائب والرسوم التي ينص عليها القانون يكون المستأجر ملزماً بدفعها جميعاً.</span>
                </div>
                
                <div class="term-item">
                    <span class="term-number">15</span>
                    <span class="term-content">لا يحق للمستأجر إظهار أي بروز للفترينات أو الديكور خارج مساحة المأجور أو استعمال الأعمدة وواجهة الجدار الفاصل بين المحلات.</span>
                </div>
                
                @if($contract->terms_and_conditions_extra)
                <div class="term-item">
                    <span class="term-number">16</span>
                    <span class="term-content">
                        <strong>شروط إضافية:</strong><br>
                        {!! nl2br(e($contract->terms_and_conditions_extra)) !!}
                    </span>
                </div>
                @endif
            </div>
            
            <div class="closing-statement">
                تليت الشروط على الأطراف وتفهموا مضمونها ومن ثم قاموا بتوقيعها.
            </div>

            <div class="signature-section">
                <table class="signature-table">
                    <tr>
                        <td>
                            <div class="signature-title">المؤجر</div>
                            @if($contract->landlord_signature_path && file_exists(public_path('uploads/' . $contract->landlord_signature_path)))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $contract->landlord_signature_path))) }}" style="max-width: 150px; max-height: 70px;" />
                            @else
                                <div class="signature-line"></div>
                            @endif
                            <div class="signature-name">
                                <span style="color:#1d4ed8;">{{ $contract->landlord_name ?? '...................................' }}</span>
                            </div>
                        </td>
                        
                        <td>
                            <div class="signature-title">المستأجر</div>
                            @if($contract->tenant_signature_path && file_exists(public_path('uploads/' . $contract->tenant_signature_path)))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $contract->tenant_signature_path))) }}" style="max-width: 150px; max-height: 70px;" />
                            @else
                                <div class="signature-line"></div>
                            @endif
                            <div class="signature-name">
                                <span style="color:#1d4ed8;">
                                    @php
                                        $tenantName = '';
                                        if (isset($contract->tenant_name_accessor) && !empty($contract->tenant_name_accessor)) {
                                            $tenantName = $contract->tenant_name_accessor;
                                        } elseif (isset($contract->tenant)) {
                                            $firstName = $contract->tenant->firstname ?? '';
                                            $lastName = $contract->tenant->lastname ?? '';
                                            $tenantName = trim($firstName . ' ' . $lastName);
                                        }
                                        echo !empty($tenantName) ? $tenantName : '...................................';
                                    @endphp
                                </span>
                            </div>
                        </td>
                        
                        <td>
                            <div class="signature-title">الشاهد الأول</div>
                            @if($contract->witness1_signature_path && file_exists(public_path('uploads/' . $contract->witness1_signature_path)))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $contract->witness1_signature_path))) }}" style="max-width: 150px; max-height: 70px;" />
                            @else
                                <div class="signature-line"></div>
                            @endif
                            <div class="signature-name">
                                <span style="color:#1d4ed8;">{{ $contract->witness1_name ?? '...................................' }}</span>
                            </div>
                        </td>
                        
                        <td>
                            <div class="signature-title">الشاهد الثاني</div>
                            @if($contract->witness2_signature_path && file_exists(public_path('uploads/' . $contract->witness2_signature_path)))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('uploads/' . $contract->witness2_signature_path))) }}" style="max-width: 150px; max-height: 70px;" />
                            @else
                                <div class="signature-line"></div>
                            @endif
                            <div class="signature-name">
                                <span style="color:#1d4ed8;">{{ $contract->witness2_name ?? '...................................' }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-logo">نظام عقاري</div>
            <p>هذا العقد محرر وفقاً لأحكام نظام الإيجار المعمول به</p>
            <p>تم إنشاء هذا العقد إلكترونياً بتاريخ {{ \Carbon\Carbon::now()->format('Y/m/d H:i') }}</p>
            <p>جميع الحقوق محفوظة © {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>