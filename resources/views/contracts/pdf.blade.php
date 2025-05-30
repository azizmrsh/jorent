<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عقد إيجار</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @php
        use Illuminate\Support\Facades\Storage;
    @endphp
    <style>
        /* 🔧 FIXED: استخدام أسماء الخطوط العربية المدعومة في gpdf مباشرة */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            /* استخدام الخطوط العربية المدعومة في gpdf */
            font-family: 'NotoNaskhArabic', 'Tajawal', 'Almarai', sans-serif !important;
        }
        
        /* 🔧 ADDED: قواعد إضافية لضمان دعم النصوص العربية */
        html, body, div, p, span, h1, h2, h3, h4, h5, h6,
        .container, .header, .content, .section-title,
        .detail-label, .detail-value, .property-title,
        .signature-title, .signature-name, .footer {
            font-family: 'NotoNaskhArabic', 'Tajawal', 'Almarai', sans-serif !important;
            direction: rtl;
            unicode-bidi: embed;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            color: #333;
            line-height: 1.8;
            padding: 20px;
            min-height: 100vh;
            direction: rtl;
            /* 🔧 FIXED: تحديد الخط العربي المدعوم بشكل صريح */
            font-family: 'NotoNaskhArabic', 'Tajawal', 'Almarai', sans-serif !important;
            unicode-bidi: embed;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 5px 25px rgba(0, 0, 100, 0.15);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #1a3c6c 0%, #0d2b52 100%);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        
        .header::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        
        .contract-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .contract-subtitle {
            font-size: 18px;
            font-weight: 300;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }
        
        .content {
            padding: 35px;
        }
        
        .section-title {
            font-size: 22px;
            color: #1a3c6c;
            margin: 30px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e3eaf3;
            position: relative;
            /* 🔧 FIXED: تحديد الخط العربي للعناوين */
            font-family: 'NotoNaskhArabic', 'Tajawal', sans-serif !important;
            font-weight: bold;
        }
        
        .section-title::after {
            content: "";
            position: absolute;
            bottom: -2px;
            right: 0;
            width: 120px;
            height: 2px;
            background: #1a3c6c;
        }
        
        .parties-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin: 25px 0;
        }
        
        .party-card {
            background: #f8fafd;
            border: 1px solid #e1e8f0;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.03);
        }
        
        .party-title {
            font-size: 20px;
            color: #1a3c6c;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #d0dcec;
            display: flex;
            align-items: center;
        }
        
        .party-title i {
            margin-left: 10px;
            font-size: 18px;
        }
        
        .party-details {
            display: grid;
            grid-template-columns: 120px 1fr;
            gap: 15px;
            margin-top: 10px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #4a6583;
            /* 🔧 FIXED: دعم الخط العربي للتسميات */
            font-family: 'NotoNaskhArabic', 'Tajawal', sans-serif !important;
        }
        
        .detail-value {
            color: #2c3e50;
            padding: 5px 0;
            border-bottom: 1px dotted #e1e8f0;
            /* 🔧 FIXED: دعم الخط العربي للقيم */
            font-family: 'NotoNaskhArabic', 'Tajawal', sans-serif !important;
        }
        
        .property-card {
            background: #f0f7ff;
            border: 1px solid #cfe1f5;
            border-radius: 10px;
            padding: 25px;
            margin: 25px 0;
        }
        
        .property-title {
            font-size: 20px;
            color: #1a3c6c;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            /* 🔧 FIXED: دعم الخط العربي لعناوين العقارات */
            font-family: 'NotoNaskhArabic', 'Tajawal', sans-serif !important;
            font-weight: bold;
        }
        
        .property-title i {
            margin-left: 10px;
            font-size: 18px;
        }
        
        .terms-container {
            margin: 30px 0;
        }
        
        .term-item {
            margin-bottom: 25px;
            padding: 0 0 0 30px;
            position: relative;
            border-left: 2px solid #e3eaf3;
        }
        
        .term-item::before {
            content: "";
            position: absolute;
            top: 8px;
            right: -10px;
            width: 20px;
            height: 20px;
            background: #1a3c6c;
            border-radius: 50%;
        }
        
        .term-number {
            position: absolute;
            top: 0;
            right: -35px;
            background: #1a3c6c;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
        }
        
        .term-content {
            background: #f8fafd;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e1e8f0;
        }
        
        .signature-section {
            margin-top: 40px;
            padding: 30px;
            background: #f8fafd;
            border-top: 2px solid #e3eaf3;
            border-bottom: 2px solid #e3eaf3;
        }
        
        .signature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        
        .signature-box {
            text-align: center;
            padding: 15px;
        }
        
        .signature-title {
            font-weight: 600;
            color: #1a3c6c;
            margin-bottom: 50px;
            /* 🔧 FIXED: دعم الخط العربي لعناوين التوقيع */
            font-family: 'NotoNaskhArabic', 'Tajawal', sans-serif !important;
        }
        
        .signature-line {
            height: 2px;
            background: #1a3c6c;
            margin: 10px 0;
        }
        
        .signature-name {
            font-size: 14px;
            color: #4a6583;
            margin-top: 5px;
            /* 🔧 FIXED: دعم الخط العربي لأسماء التوقيع */
            font-family: 'NotoNaskhArabic', 'Tajawal', sans-serif !important;
        }
        
        .footer {
            padding: 25px;
            text-align: center;
            background: #f0f7ff;
            color: #4a6583;
            font-size: 14px;
            border-top: 1px solid #e1e8f0;
        }
        
        .footer-logo {
            font-size: 22px;
            font-weight: bold;
            color: #1a3c6c;
            margin-bottom: 10px;
        }
        
        .print-btn {
            display: inline-block;
            background: #1a3c6c;
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s ease;
            border: 2px solid #1a3c6c;
            cursor: pointer;
        }
        
        .print-btn:hover {
            background: white;
            color: #1a3c6c;
        }
        
        .contract-number {
            text-align: left;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 15px;
        }
        
        .dynamic-field {
            color: #1e40af !important;
            font-weight: 600;
            background-color: rgba(30, 64, 175, 0.05);
            padding: 2px 4px;
            border-radius: 3px;
        }
        
        .closing-statement {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }
        
        .no-print {
            text-align: left;
            margin-bottom: 15px;
            padding: 0 35px;
            padding-top: 20px;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
                font-size: 11pt;
                color: #000;
            }
            
            .container {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
            }
            
            .print-btn {
                display: none;
            }
            
            .no-print {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .parties-container {
                grid-template-columns: 1fr;
            }
            
            .signature-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print">
            <button onclick="window.print()" class="print-btn">
                <i class="fas fa-print"></i> طباعة العقد
            </button>
        </div>

        <div class="header">
            <h1 class="contract-title">عقد إيجار رسمي</h1>
            <p class="contract-subtitle">محرر بتاريخ {{ \Carbon\Carbon::now()->format('Y/m/d') }}</p>
        </div>
        
        <div class="content">
            <div class="contract-number">رقم العقد: {{ $contract->id ?? 'غير محدد' }}</div>
            
            <h2 class="section-title">أطراف العقد</h2>
            
            <div class="parties-container">
                <div class="party-card">
                    <h3 class="party-title"><i class="fas fa-user-tie"></i> الطرف الأول (المؤجر)</h3>
                    <div class="party-details">
                        <div class="detail-label">الاسم:</div>
                        <div class="detail-value"><span class="dynamic-field">{{ $contract->landlord_name ?? 'غير محدد' }}</span></div>
                    </div>
                </div>
                
                <div class="party-card">
                    <h3 class="party-title"><i class="fas fa-user"></i> الطرف الثاني (المستأجر)</h3>
                    <div class="party-details">
                        <div class="detail-label">الاسم:</div>
                        <div class="detail-value">
                            <span class="dynamic-field">
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
                        </div>
                    </div>
                </div>
            </div>
            
            <h2 class="section-title">تفاصيل المأجور</h2>
            
            <div class="property-card">
                <h3 class="property-title"><i class="fas fa-home"></i> وصف المأجور</h3>
                <div class="party-details">
                    <div class="detail-label">الموقع:</div>
                    <div class="detail-value"><span class="dynamic-field">{{ $contract->property_name_accessor ?? ($contract->property->name ?? 'غير محدد') }}</span></div>
                    
                    <div class="detail-label">الوحدة:</div>
                    <div class="detail-value"><span class="dynamic-field">{{ $contract->unit_name_accessor ?? ($contract->unit->name ?? 'غير محدد') }}</span></div>
                    
                    <div class="detail-label">نوع الاستخدام:</div>
                    <div class="detail-value"><span class="dynamic-field">{{ $contract->unit->usage_type ?? 'سكني' }}</span></div>
                </div>
            </div>
            
            <div class="party-details">
                <div class="detail-label">تاريخ ابتداء الإيجار:</div>
                <div class="detail-value"><span class="dynamic-field">{{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('Y/m/d') : 'غير محدد' }}</span></div>
                
                <div class="detail-label">تاريخ انتهاء الإيجار:</div>
                <div class="detail-value"><span class="dynamic-field">{{ $contract->end_date ? \Carbon\Carbon::parse($contract->end_date)->format('Y/m/d') : 'غير محدد' }}</span></div>
                
                <div class="detail-label">مدة الإيجار:</div>
                <div class="detail-value">
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
                </div>
                
                <div class="detail-label">بدل الإيجار السنوي:</div>
                <div class="detail-value"><span class="dynamic-field">{{ number_format($contract->rent_amount ?? 0, 2) }} دينار أردني</span></div>
                
                <div class="detail-label">كيفية أداء البدل:</div>
                <div class="detail-value"><span class="dynamic-field">{{ $contract->payment_method ?? 'شهري مقدم، يستحق في اليوم الأول من كل شهر ميلادي' }}</span></div>
            </div>
            
            <h2 class="section-title">شروط وأحكام العقد</h2>
            
            <div class="terms-container">
                <div class="term-item">
                    <div class="term-number">1</div>
                    <div class="term-content">
                        <p>استلم المستأجر المأجور سالماً من كل عيب تام أو خلل ويتعهد بتسليمه عند إنتهاء مدة الإجارة كما استلمه.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">2</div>
                    <div class="term-content">
                        <p>عند إنتهاء مدة الإجارة، على المستأجر أن يأخذ وصلاً خطياً من المؤجر يتضمن استلامه للمأجور وتوابعه سالماً. في حال وجود أي عيب أو تلف، يحق للمؤجر إصلاحه على نفقة المستأجر.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">3</div>
                    <div class="term-content">
                        <p>ليس للمستأجر الحق بتأجير المأجور كلياً أو جزء منه للغير أو المشاركة مع الغير بدون موافقة المؤجر الخطية.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">4</div>
                    <div class="term-content">
                        <p>لا يحق للمستأجر إحداث أي تغيير في المأجور من هدم أو بناء أو فتح شبابيك أو تغيير في الأبواب أو الحنفيات إلا بموافقة المؤجر الخطية.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">5</div>
                    <div class="term-content">
                        <p>عموم ما يحصل في المأجور من عطل أو عيب كخراب في المجاري أو التمديدات فيعود تصليحه على المستأجر ولا يحق له مطالبة المؤجر بأي تعويضات.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">6</div>
                    <div class="term-content">
                        <p>إذا امتنع أو تأخر المستأجر عن دفع أي قسط من الأقساط في ميعاد استحقاقه، تصبح جميع الأقساط الأخرى مستحقة الأداء حالاً، وللمؤجر حق فسخ العقد واستلام المأجور.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">7</div>
                    <div class="term-content">
                        <p>بحال حدوث أي مما ذكر في البندين الخامس والسادس، يحق للمؤجر وضع يده على أموال المستأجر الموجودة في المأجور وبيعها واستيفاء حقوقه من ثمنها.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">8</div>
                    <div class="term-content">
                        <p>للمؤجر الحق في بناء طوابق علوية فوق المأجور أو بالقرب منه وعمل جميع التصليحات التي يريدها دون أن يكون للمستأجر حق بطلب تعويض أو تنزيل أجرة.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">9</div>
                    <div class="term-content">
                        <p>جميع ما يعمله المستأجر من تنظيمات أو تصليحات ووضع بورسلين أو غيره تكون نفقتها عليه وعند خروجه يكون المؤجر مخيراً بين أخذها بدون مقابل أو طلب إعادة المأجور إلى حالته الأصلية على حساب المستأجر.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">10</div>
                    <div class="term-content">
                        <p>لا يجوز للمستأجر أن يشغل المأجور لغير الغاية التي استأجره لأجلها أو أن يستعمله فيما يخالف الشرع والقانون وأنظمة البلاد والآداب العامة.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">11</div>
                    <div class="term-content">
                        <p>في حالة تعدد المستأجرين، يعتبرون متكافلين ومتضامنين في جميع أحكام العقد والتزاماته، وأي تبليغ لأحدهم يعتبر تبليغاً للجميع.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">12</div>
                    <div class="term-content">
                        <p>لا حاجة لتبادل أي إخطار أو إنذار بين الطرفين إلا في الحالات التي نص فيها العقد على ذلك.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">13</div>
                    <div class="term-content">
                        <p>يسقط المستأجر من الآن ادعاء كذب الإقرار في هذا العقد كلياً أو جزئياً وفيما يتفرع عنه من كمبيالات وشيكات ومستندات.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">14</div>
                    <div class="term-content">
                        <p>ثمن المياه والكهرباء وجميع الضرائب والرسوم التي ينص عليها القانون يكون المستأجر ملزماً بدفعها جميعاً.</p>
                    </div>
                </div>
                
                <div class="term-item">
                    <div class="term-number">15</div>
                    <div class="term-content">
                        <p>لا يحق للمستأجر إظهار أي بروز للفترينات أو الديكور خارج مساحة المأجور أو استعمال الأعمدة وواجهة الجدار الفاصل بين المحلات.</p>
                    </div>
                </div>
                
                @if($contract->terms_and_conditions_extra)
                <div class="term-item">
                    <div class="term-number">16</div>
                    <div class="term-content">
                        <p><strong>شروط إضافية:</strong></p>
                        <div style="margin-top: 10px; line-height: 1.8; padding-right: 15px;">
                            {!! nl2br(e($contract->terms_and_conditions_extra)) !!}
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <div class="closing-statement">
                تليت الشروط على الأطراف وتفهموا مضمونها ومن ثم قاموا بتوقيعها.
            </div>

            <div class="signature-section">
                <div class="signature-grid">
                    <div class="signature-box">
                        <div class="signature-title">المؤجر</div>
                        @if($contract->landlord_signature_path && Storage::disk('public')->exists($contract->landlord_signature_path))
                            <img src="{{ asset('storage/' . $contract->landlord_signature_path) }}" alt="توقيع المؤجر" style="max-width: 150px; max-height: 70px; margin: 0 auto 10px auto; display: block;">
                        @else
                            <div class="signature-line"></div>
                        @endif
                        <div class="signature-name"><span class="dynamic-field">{{ $contract->landlord_name ?? '...................................' }}</span></div>
                    </div>
                    
                    <div class="signature-box">
                        <div class="signature-title">المستأجر</div>
                        @if($contract->tenant_signature_path && Storage::disk('public')->exists($contract->tenant_signature_path))
                            <img src="{{ asset('storage/' . $contract->tenant_signature_path) }}" alt="توقيع المستأجر" style="max-width: 150px; max-height: 70px; margin: 0 auto 10px auto; display: block;">
                        @else
                            <div class="signature-line"></div>
                        @endif
                        <div class="signature-name">
                            <span class="dynamic-field">
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
                    </div>
                    
                    <div class="signature-box">
                        <div class="signature-title">شاهد</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">...................................</div>
                    </div>
                    
                    <div class="signature-box">
                        <div class="signature-title">شاهد</div>
                        <div class="signature-line"></div>
                        <div class="signature-name">...................................</div>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <button onclick="window.print()" class="print-btn">
                    <i class="fas fa-print"></i> طباعة العقد
                </button>
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