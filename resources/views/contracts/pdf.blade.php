<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عقد إيجار</title>
    <style>
        /* Enhanced Arabic fonts using gpdf built-in fonts */
        @font-face {
            font-family: 'ArabicFont';
            src: url('{{ public_path('vendor/gpdf/fonts/NotoSansArabic-Regular.ttf') }}') format('truetype');
            font-weight: normal;
        }
        
        @font-face {
            font-family: 'ArabicFont';
            src: url('{{ public_path('vendor/gpdf/fonts/NotoSansArabic-Bold.ttf') }}') format('truetype');
            font-weight: bold;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'ArabicFont', 'NotoSansArabic', 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 14px;
            line-height: 1.8;
            color: #333;
            background: #fff;
        }
        
        .contract-container {
            max-width: 800px; /* يمكنك تعديل هذا حسب الرغبة */
            margin: 20px auto; /* إضافة هامش علوي وسفلي للصفحة */
            padding: 25px;
            border: 1px solid #ccc; /* إطار خفيف حول العقد */
        }
        
        .basmala { /* تنسيق خاص للبسملة */
            text-align: center;
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .contract-main-title { /* عنوان العقد الرئيسي */
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 25px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
        }
        
        .contract-info-table, .party-info-table { /* استخدام جداول لتنسيق المعلومات الأساسية ومعلومات الأطراف */
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse; /* لدمج حدود الخلايا */
        }

        .contract-info-table td, .party-info-table td {
            padding: 8px 5px;
            border: 1px solid #e2e8f0; /* حدود خفيفة للخلايا */
            vertical-align: top; /* محاذاة النص لأعلى الخلية */
        }

        .contract-info-table td:first-child, .party-info-table td:first-child { /* تنسيق خاص لأسماء الحقول */
            font-weight: 600;
            color: #374151;
            width: 180px; /* تحديد عرض لأسماء الحقول */
        }
        .contract-info-table .info-value, .party-info-table .info-value {
            font-weight: normal;
            color: #1e40af; /* Blue color for dynamic content */
            font-size: 15px;
        }

        /* Special styling for dynamic content */
        .dynamic-field {
            color: #1e40af !important;
            font-weight: 600;
            background-color: rgba(30, 64, 175, 0.05);
            padding: 2px 4px;
            border-radius: 3px;
        }


        .preamble { /* تنسيق الديباجة */
            margin-top: 20px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        .terms-section {
            margin: 25px 0;
        }
        
        .terms-title {
            font-size: 18px;
            font-weight: 700; /* زيادة سماكة الخط */
            color: #1e40af;
            margin-bottom: 15px;
            text-align: right; /* محاذاة لليمين بدلاً من الوسط */
            border-bottom: 1px solid #3b82f6;
            padding-bottom: 8px;
        }
        
        .terms-content {
            /* background: #f9fafb; */ /* إزالة الخلفية لجعلها متناسقة أكثر مع شكل الـ PDF */
            /* border: 1px solid #e5e7eb; */ /* إزالة الإطار */
            border-radius: 6px;
            padding: 10px 0; /* تقليل الحشو العلوي والسفلي */
            line-height: 1.9; /* زيادة تباعد الأسطر للبنود */
        }
        .terms-content p {
            margin-bottom: 12px; /* زيادة المسافة بين الفقرات/البنود */
        }
        
        .signatures-section {
            margin-top: 40px;
            page-break-inside: avoid; /* محاولة تجنب كسر الصفحة داخل قسم التوقيعات */
        }
        
        .closing-statement { /* تنسيق عبارة "تليت الشروط..." */
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .signatures-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* تقسيم مرن للتوقيعات */
            gap: 30px; /* زيادة المسافة بين مربعات التوقيع */
            margin-top: 20px;
        }
        
        .signature-box {
            /* border: 1px solid #d1d5db; */ /* إزالة الإطار ليتوافق مع شكل الـ PDF */
            padding: 10px;
            text-align: center;
            min-height: 100px; /* تقليل الارتفاع الأدنى */
        }
        
        .signature-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 40px; /* زيادة المسافة لترك مكان للتوقيع الفعلي */
            font-size: 14px;
        }
        
        .signature-img {
            max-width: 150px;
            max-height: 70px; /* تقليل ارتفاع الصورة إذا وجدت */
            margin: 0 auto 10px auto; /* تعديل الهوامش */
            display: block;
            border: 1px solid #e5e7eb;
        }

        .contract-footer-final { /* تذييل شركة حماة الحق */
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
        }

        .contract-number { /* تم نقله ليكون أقل بروزًا، يمكنك تعديل مكانه */
            text-align: left;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 15px;
        }
        
        @media print {
            body {
                font-size: 11pt; /* تعديل حجم الخط للطباعة ليتناسب مع محتوى الصفحة */
                color: #000; /* استخدام اللون الأسود للطباعة */
            }
            
            .contract-container {
                margin: 0;
                padding: 10px;
                border: none; /* إزالة الإطار عند الطباعة */
                box-shadow: none; /* إزالة الظل عند الطباعة */
            }
            
            .no-print { /* فئة لإخفاء العناصر عند الطباعة، مثل زر الطباعة */
                display: none !important;
            }
            .contract-main-title, .terms-title { /* التأكد من ألوان العناوين للطباعة */
                color: #000;
                border-bottom: 1px solid #000;
            }
            .contract-info-table td, .party-info-table td {
                border: 1px solid #ccc; /* حدود أغمق قليلاً للجداول في الطباعة */
            }
        }

    </style>
</head>
<body>
    <div class="contract-container">
        <div class="no-print" style="text-align: left; margin-bottom: 15px;">
            <button onclick="window.print()" style="padding: 8px 15px; background-color: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">طباعة العقد</button>
        </div>

        <div class="contract-number">رقم العقد: {{ $contract->id ?? 'غير محدد' }}</div>
        
        <div class="basmala">{{-- بسم الله الرحمن الرحيم --}}بسم الله الرحمن الرحيم</div> [cite: 1, 25]
        <div class="contract-main-title">{{-- عقد إيجار --}}عقد إيجار</div> [cite: 1, 25]
        
        <table class="contract-info-table">
            <tr>
                <td>المؤجر:</td>
                <td class="info-value"><span class="dynamic-field">{{ $contract->landlord_name ?? 'غير محدد' }}</span></td>
            </tr>            
            <tr>
                <td>المستأجر:</td>
                <td class="info-value"><span class="dynamic-field">{{ $contract->tenant_name_accessor ?? ($contract->tenant->firstname ?? '' . ' ' . $contract->tenant->lastname ?? '') ?? 'غير محدد' }}</span></td>
            </tr>
            <tr>
                <td>أوصاف المأجور:</td>
                <td class="info-value"><span class="dynamic-field">{{ $contract->property_name_accessor ?? ($contract->property->name ?? 'غير محدد') }} - {{ $contract->unit_name_accessor ?? ($contract->unit->name ?? 'غير محدد') }}</span></td>
            </tr>
            <tr>
                <td>مقدار الإيجار:</td>
                <td class="info-value"><span class="dynamic-field">{{ number_format($contract->rent_amount ?? 0, 2) }} دينار أردني</span></td>
            </tr>
            <tr>
                <td>تاريخ ابتداء الإيجار:</td>
                <td class="info-value"><span class="dynamic-field">{{ $contract->start_date ? \Carbon\Carbon::parse($contract->start_date)->format('Y/m/d') : 'غير محدد' }}</span></td>
            </tr>
             <tr>
                <td>مدة الإيجار:</td>
                <td class="info-value">
                     @if ($contract->start_date && $contract->end_date)
                        @php
                            $startDate = \Carbon\Carbon::parse($contract->start_date);
                            $endDate = \Carbon\Carbon::parse($contract->end_date);
                            // Calculate difference
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
                </td>
            </tr>
            <tr>
                <td>استعمال المأجور:</td>
                <td class="info-value">{{ $contract->unit->usage_type ?? 'سكني' }} {{-- افتراض أنه سكني إذا لم يحدد --}}</td>
            </tr>
            <tr>
                <td>كيفية دفع بدل الإيجار:</td>
                <td class="info-value">{{ $contract->payment_method ?? 'شهري مقدم، يستحق في اليوم الأول من كل شهر ميلادي' }} {{-- يمكنك تعديل القيمة الافتراضية --}}</td>
            </tr>
        </table>
        
        <div class="preamble">
            حيث ان الطرف الأول يملك العقار الموصوف اعلاه وحيث ان الطرف الثاني يرغب باستئجاره، فقد اتفق الطرفين على ما يلي: [cite: 1, 26]
        </div>

        <div class="terms-section">
            <div class="terms-title">شروط العقد</div>
            
            <div class="terms-content">
                <p><strong>أولاً:</strong> تعتبر مقدمة هذا العقد وشروطه وملحقاته أن وجد جزءا لا يتجزأ منه وتقرأ معه كوحدة واحدة.</p> [cite: 1, 26]
                <p><strong>ثانياً:</strong> يقر المستأجر بأنه قد استلم المأجور وملحقاته سالما من كل عيب وقد عاين بنفسه كافة الابواب والشبابيك والزجاج والغالات بمفاتيحها والمغاسل والحنفيات والادوات الصحية والدهان والبلاط والسيراميك والجبصين وكامل الديكورات وان جميع هذه الأشياء والتوابع جديدة وسليمة وخالية من أي عيب أو خلل ويتعهد المستأجر بتسليمها عند انتهاء مدة الإجارة جديدة بالحالة التي استلمها بها.</p> [cite: 3, 27]
                <p><strong>ثالثاً:</strong> يجب على المستأجر قبل انتهاء مدة العقد إذا كان لا يرغب بالتجديد لمدة مماثلة أن يقوم بتبليغ المؤجر قبل انتهاء مدة العقد بشهرين على الأقل وإلا يعتبر مستأجرا للعقار لمدة مماثلة أخرى إذا أراد المؤجر ذلك، مع التأكيد على عدم انطباق هذا الشرط على المؤجر.</p> [cite: 4, 28]
                <p><strong>رابعاً:</strong> لا يجوز للمستأجر تأجير المأجور أو جزء منه للغير أو إدخال شريك أو شركة معه في المأجور أو التخلي عنه كليا أو جزئيا للغير بدون موافقة المؤجر الخطية.</p> [cite: 5, 29]
                <p><strong>خامساً:</strong> لا يحق للمستأجر أن يحدث أي تغيير في المأجور من هدم، أو بناء، أو فتح شبابيك، أو إحداث سدة، أو إحداث أي تغيير في الأبواب أو الحنفيات أو او ثقب الجدران وغيرها إلا بموافقة المؤجر الخطية، وفي كل الأحوال على ان يقوم بإعادتها على نفقته الى الحالة التي استلمها عليه عند توقيعه للعقد، ويجب على المستأجر إعادة أي ملحقات استلمها مع المأجور بالحالة التي استلمها بها.</p> [cite: 6, 30]
                <p><strong>سادساً:</strong> كل ما يحصل في المأجور من عطل، أو عيب، أو خراب، أو تلف في المجاري، أو التمديدات الصحية، أو الكهربائية، أو القصارة، أو التشطيبات، أو أي من المرافق الملحقة بالمأجور فيعود تصليحها على المستأجر ولا يحق له أن يطالب المؤجر بشيء من التعويضات كما لا يحق له أن يطالب المؤجر بأي تعويضات أو ضرر أو عطل مهما كان نوعه بسبب أي تعطيل أو خلل يحصل في الخدمات المشتركة الملحقة بالعمارة.</p> [cite: 7, 31]
                <p><strong>سابعاً:</strong> يلتزم المستأجر بدفع كافة الرسوم والمصاريف والنفقات والفواتير المفروضة على المأجور بما فيها أجور الحراسة والنظافة والكهرباء والهاتف وضريبة المسقفات وضريبة المعارف بالإضافة الى كافة نفقات الصيانة وغيرها.</p> [cite: 8, 32]
                <p><strong>ثامناً:</strong> إذا امتنع أو تأخر المستأجر عن دفع أي قسط من أقساط بدل الإيجار بعد مرور عشرة أيام على ميعاد استحقاقه فتصبح جميع أقساط العقد مستحقة الدفع فورا ودفعة واحدة، وللمؤجر أيضا الحق والخيار بفسخ هذا العقد واستلام المأجور ولو أن مدة الإجارة لم تنته كما وله الحق بوضع يده عليه واجارته للغير بالبدل الذي يراه مناسبا على أن يعود بالفرق بين البدلين على المستأجر بحال نقصان البدل الثاني عن الأول.</p> [cite: 9, 33]
                <p><strong>تاسعاً:</strong> بحال حدوث أمر من الأمرين المذكورين في البندين السابقين من هذا العقد فان للمؤجر الحق أيضا بوضع يده على أموال المستأجر الموجودة في المأجور وبيعها بالثمن الذي يراه مناسبا واستيفاء حقوقه من ثمنها.</p> [cite: 10, 34]
                <p><strong>عاشراً:</strong> للمؤجر الحق أن يبني طوابق علوية فوق المأجور أو بالقرب منه وأن يعمل جميع التصليحات والترميمات التي يريدها في المأجور وتوابعه أو بقربه مهما اقتضى لها من الوقت في مدة هذه الإجارة أو في المدة التي تمتد إليها ولا يجوز للمستأجر في ذلك الحال أن يطالب المؤجر بالتعويض عن أي عطل أو ضرر أو تنزيل في الأجرة بسبب هذه الأعمال.</p> [cite: 12, 36]
                <p><strong>الحادي عشر:</strong> جميع ما يقوم به المستأجر من تحسينات، أو تصليحات، أو أعمال ديكور، أو غيره تكون نفقتها عليه وحده وعند خروجه يكون المؤجر مخيرا إما بأخذها كما هي بدون مقابل أو بطلب إعادة المأجور كما ما كان عليه لحظة هذا العقد، وفي ذلك الحال تكون نفقات إعادة الحال وإزالتها مهما بلغت على نفقة المستأجر وحده.</p> [cite: 13, 37]
                <p><strong>الثاني عشر:</strong> لا يجوز للمستأجر أن يشغل العقار المستأجر لغير الغاية التي استأجر لها أو أن يستعمله فيما يخالف الشرع والقانون والنظام العام والآداب العامة، ولا يجوز له إحداث الضوضاء أو التسبب في الإزعاج للمجاورين.</p> [cite: 14, 38]
                <p><strong>الثالث عشر:</strong> إذا كان المستأجرين في هذا العقد أكثر من شخص واحد فيعتبرون متكافلين ومتضامنين في كل ما ينشأ عنه من التزامات، وإذا كان المستأجر شركة أو شخص معنوي فان الشخص أو الأشخاص الذين يوقعون عن الشركة أو المؤسسة (الشخص المعنوي) يعتبرون مسؤولا و / أو مسؤولين بالتكافل والتضامن معها بجميع مسؤوليات المستأجر في هذا العقد وما يترتب عليه من الالتزامات فيه طيلة مدة هذا العقد وأية مدد أخرى يتجدد إليها.</p> [cite: 15, 39]
                <p><strong>الرابع عشر:</strong> في حال رغب المؤجر انهاء العقد في نهاية مدته او لم يرغب بتجديد العقد لمدة مماثلة فيعفى من توجيه الإنذار الذي يتطلبه قانون المالكين والمستأجرين ويجوز له تقديم طلب مستعجل لإنهاء العقد مباشرة بعد انتهاء المهلة التي حددها القانون.</p> [cite: 16, 40]
                <p><strong>الخامس عشر:</strong> لا يجوز للمستأجر أن يخالف أحكام البناء والتنظيم ويكون ملزما بتحمل أية مخالفة أو غرامة ناجمة عن مخالفة القوانين، أو الأنظمة، أو تعليمات البلديات، أو أحكام قانون الطوابق والشقق او امانة عمان وذلك عن طيلة فترة اشغاله للعقار.</p> [cite: 17, 41]
                <p><strong>السادس عشر:</strong> يلتزم المستأجر بنهاية مدة العقد بإحضار براءة ذمه للمؤجر من شركة الكهرباء وسلطة المياه والبلدية يثبت فيها عدم وجود اية مبالغ مترتبة على المأجور خلال فترة الإيجار.</p> [cite: 18, 42]
                <p><strong>السابع عشر:</strong> للمؤجر الحق في تحديد اماكن وضع صحون الستالايت واللواقط الإلكترونية والإذاعية وخزانات الماء ولا يحق للمستأجر إضافة خزانات مياه إضافية بدون موافقة المؤجر الخطية مهما كانت غاية الإيجار.</p> [cite: 19, 43]
                <p><strong>الثامن عشر:</strong> اذا كان العقار المؤجر شقة فيلتزم المستأجر بأحكام قانون الملكية العقارية ونظام إدارة الشقق و يلتزم بدفع ما يترتب على الشقة من مستحقات تفرض على ادارة او استعمال الخدمات المشتركة واذا كان للبناية حارس او عامل نظافة فيلزم بدفع مستحقاته ويلتزم بدفع اية نفقات لصيانة الخدمات المشتركة بما فيها صيانة المصعد او صيانة السطح حتى لو لم يكن يستخدمهما و يلتزم بدفع نسبته من فواتير المياه والكهرباء التي تستحق على الخدمات المشتركة، ولا يجوز له باي حال من الاحوال رفض المشاركة في مصاريف الخدمات المشتركة و لا يجوز له التذرع بعدم الاستفادة منها و يجب عليه أن يتقيد بالمكان المخصص لاصطفاف سيارته و لا يجوز له التعدي على الكراجات المخصصة لغيره من السكان.</p> [cite: 20, 21, 44, 45]
                <p><strong>التاسع عشر:</strong> إن عدم احترام الجوار الساكنين في البناية التي تقع بها الشقة أو التي تقابلهم أو إيذاء أي من الجوار بأي أفعال لا يتقبلها العرف والعادة يعتبر سببا لفسخ العقد ويلزم المستأجر بالتعويض عن أي عطل أو ضرر يلحق بالمالك أو بالآخرين.</p> [cite: 22, 46]
                
                @if($contract->terms_and_conditions_extra)
                <div style="margin-top: 20px; padding-top:15px; border-top: 1px dashed #ccc;">
                    <p><strong>شروط إضافية (خصوصية):</strong></p> [cite: 23, 47]
                    <div style="margin-top: 10px; line-height: 1.8; padding-right: 15px;">
                        {!! nl2br(e($contract->terms_and_conditions_extra)) !!}
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="closing-statement">
            تليت الشروط على الأطراف وتفهموا مضمونها ومن ثم قاموا بتوقيعها. [cite: 23, 47]
        </div>

        <div class="signatures-section">
            <div class="signatures-grid">
                <div class="signature-box">
                    <div class="signature-label">المؤجر:</div> [cite: 24, 48]
                    @if($contract->landlord_signature_path && Storage::disk('public')->exists($contract->landlord_signature_path))
                        <img src="{{ asset('storage/' . $contract->landlord_signature_path) }}" alt="توقيع المؤجر" class="signature-img">
                    @endif
                    <div><span class="dynamic-field">{{ $contract->landlord_name ?? '...................................' }}</span></div>
                </div>
                
                <div class="signature-box">
                    <div class="signature-label">المستأجر:</div> [cite: 24, 48]
                    @if($contract->tenant_signature_path && Storage::disk('public')->exists($contract->tenant_signature_path))
                        <img src="{{ asset('storage/' . $contract->tenant_signature_path) }}" alt="توقيع المستأجر" class="signature-img">
                    @endif
                    <div><span class="dynamic-field">{{ $contract->tenant_name_accessor ?? ($contract->tenant->firstname ?? '' . ' ' . $contract->tenant->lastname ?? '') ?? '...................................' }}</span></div>
                </div>
                
                <div class="signature-box">
                    <div class="signature-label">شاهد:</div> [cite: 24, 48]
                    {{-- افترض وجود حقل لتوقيع الشاهد الأول إذا أردت عرض صورة توقيع --}}
                    {{-- @if($contract->witness1_signature_path && Storage::disk('public')->exists($contract->witness1_signature_path))
                        <img src="{{ asset('storage/' . $contract->witness1_signature_path) }}" alt="توقيع الشاهد الأول" class="signature-img">
                    @endif --}}
                    <div>...................................</div>
                </div>
                <div class="signature-box">
                    <div class="signature-label">شاهد:</div> [cite: 24, 48]
                    {{-- افترض وجود حقل لتوقيع الشاهد الثاني --}}
                    {{-- @if($contract->witness2_signature_path && Storage::disk('public')->exists($contract->witness2_signature_path))
                        <img src="{{ asset('storage/' . $contract->witness2_signature_path) }}" alt="توقيع الشاهد الثاني" class="signature-img">
                    @endif --}}
                    <div>...................................</div>
                </div>
            </div>
        </div>
        
        <div class="contract-footer-final">
            <p>شركة حماة الحق للمحاماة</p> [cite: 11, 24, 35, 48]
            <p>نموذج الكتروني - لعقد إيجار - صياغة وإعداد شركة حماة الحق، سنة 2021 ©</p> [cite: 11, 24, 35, 48]
        </div>

        <div style="margin-top: 30px; text-align: center; padding-top: 20px; border-top: 1px solid #e2e8f0;">
            <p style="font-size: 12px; color: #6b7280;">
                تم إنشاء هذا العقد إلكترونياً بتاريخ {{ \Carbon\Carbon::now()->format('Y/m/d H:i') }}
            </p>
            <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                نظام إدارة العقارات - jhome
            </p>
        </div>
    </div>
</body>
</html>