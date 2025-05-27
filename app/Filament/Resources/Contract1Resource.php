<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Contract1Resource\Pages;
use App\Models\Contract1;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

// Export functionality imports
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;



class Contract1Resource extends Resource
{
    protected static ?string $model = Contract1::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Rental Management';
    protected static ?string $navigationLabel = 'Contract1s';

    public static function form(Form $form): Form
    {
        return $form->schema([

            /// Landlord and Tenant Info
            Forms\Components\Section::make('Landlord and Tenant Info')->schema([
                Forms\Components\TextInput::make('landlord_name')->required(),

              Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'firstname')
                    ->searchable()
                    ->required(),  
            ])->columns(2),

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /// Property & Unit        

            Forms\Components\Section::make('Property & Unit')->schema([
                Forms\Components\Select::make('property_id')
                    ->options(Property::all()->pluck('name', 'id'))
                    ->required()
                    ->label('Property')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $property = Property::with('address')->find($state);
                        if ($property?->address) {
                            $set('governorate', $property->address->governorate);
                            $set('city', $property->address->city);
                            $set('district', $property->address->district);
                            $set('building_number', $property->address->building_number);
                            $set('plot_number', $property->address->plot_number);
                            $set('basin_number', $property->address->basin_number);
                            $set('property_number', $property->address->property_number);
                            $set('street_name', $property->address->street_name);
                        }
                        $set('unit_id', null);
                    }),
                Forms\Components\Select::make('unit_id')
                    ->options(fn (callable $get) =>
                        Unit::where('property_id', $get('property_id'))->pluck('name', 'id')
                    )
                    ->required()
                    ->label('Unit'),

                Forms\Components\TextInput::make('governorate')->label('Governorate')->readOnly(),
                Forms\Components\TextInput::make('city')->label('City')->readOnly(),
                Forms\Components\TextInput::make('district')->label('District')->readOnly(),
                Forms\Components\TextInput::make('building_number')->label('Building Number')->readOnly(),
                Forms\Components\TextInput::make('plot_number')->label('Plot Number')->readOnly(),
                Forms\Components\TextInput::make('basin_number')->label('Basin Number')->readOnly(),
                Forms\Components\TextInput::make('property_number')->label('Property Number')->readOnly(),
                Forms\Components\TextInput::make('street_name')->label('Street Name')->readOnly(),
            ])->columns(3),
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /// Contract Details
            Forms\Components\Section::make('Contract Details')->schema([
                Forms\Components\DatePicker::make('start_date')->required(),
                Forms\Components\DatePicker::make('end_date')->required(),
                Forms\Components\DatePicker::make('due_date')->label('Due Date'),
                Forms\Components\TextInput::make('rent_amount')->numeric()->required(),
                Forms\Components\Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->reactive()
                    ->afterStateHydrated(function (callable $set, callable $get) {
                        $startDate = $get('start_date');
                        $endDate = $get('end_date');

                        if ($startDate && $endDate && now()->between($startDate, $endDate)) {
                            $set('status', 'active');
                        } else {
                            $set('status', 'inactive');
                        }
                    }),
            ])->columns(3),

 Forms\Components\Section::make('Terms and Conditions')->schema([
                Forms\Components\Textarea::make('terms_and_conditions_extra'),
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('show_terms')
                        ->label('عرض الشروط الافتراضية')
                        ->color('primary')
                        ->modalHeading('الشروط والأحكام الافتراضية')
                        ->modalContent(fn() => new \Illuminate\Support\HtmlString('<div style="text-align:right;direction:rtl;font-size:15px;white-space:pre-line;line-height:2;">
<strong>أولا:</strong> تعتبر مقدمة هذا العقد وشروطه وملحقاته أن وجد جزءا لا يتجزأ منه وتقرأ معه كوحدة واحدة
<strong>ثانيا:</strong> يقر المستأجر بأنه قد استلم المأجور وملحقاته سالما من كل عيب وقد عاين بنفسه كافة الابواب والشبابيك والزجاج والغالات بمفاتيحها والمغاسل والحنفيات والادوات الصحية والدهان والبلاط والسيراميك والجبصين وكامل الديكورات وان جميع هذه الأشياء والتوابع جديدة وسليمة وخالية من أي عيب أو خلل ويتعهد المستأجر بتسليمها عند انتهاء مدة الإجارة جديدة بالحالة التي استلمها بها.
<strong>ثالثا:</strong> يجب على المستأجر قبل انتهاء مدة العقد إذا كان لا يرغب بالتجديد لمدة مماثلة أن يقوم بتبليغ المؤجر قبل انتهاء مدة العقد بشهرين على الأقل وإلا يعتبر مستأجرا للعقار لمدة مماثلة أخرى إذا أراد المؤجر ذلك، مع التأكيد على عدم انطباق هذا الشرط على المؤجر
<strong>رابعا:</strong> لا يجوز للمستأجر تأجير المأجور أو جزء منه للغير أو إدخال شريك أو شركة معه في المأجور أو التخلي عنه كليا أو جزئيا للغير بدون موافقة المؤجر الخطية
<strong>خامسا:</strong> لا يحق للمستأجر أن يحدث أي تغيير في المأجور من هدم، أو بناء، أو فتح شبابيك، أو إحداث سدة، أو إحداث أي تغيير في الأبواب أو الحنفيات أو أو ثقب الجدران وغيرها إلا بموافقة المؤجر الخطية، وفي كل الأحوال على أن يقوم بإعادتها على نفقته إلى الحالة التي استلمها عليه عند توقيعه للعقد، ويجب على المستأجر إعادة أي ملحقات استلمها مع المأجور بالحالة التي استلمها بها.
<strong>سادسا:</strong> كل ما يحصل في المأجور من عطل، أو عيب، أو خراب، أو تلف في المجاري، أو التمديدات الصحية، أو الكهربائية أو القصارة، أو التشطيبات، أو أي من المرافق الملحقة بالمأجور فيعود تصليحها على المستأجر ولا يحق له أن يطالب المؤجر بشيء من التعويضات كما لا يحق له أن يطالب المؤجر بأي تعويضات أو ضرر أو عطل مهما كان نوعه بسبب أي تعطيل أو خلل يحصل في الخدمات المشتركة الملحقة بالعمارة
<strong>سابعا:</strong> يلتزم المستأجر بدفع كافة الرسوم والمصاريف والنفقات والفواتير المفروضة على المأجور بما فيها أجور الحراسة والنظافة والكهرباء والهاتف وضريبة المسقفات وضريبة المعارف بالإضافة إلى كافة نفقات الصيانة وغيرها.
<strong>ثامنا:</strong> إذا امتنع أو تأخر المستأجر عن دفع أي قسط من أقساط بدل الإيجار بعد مرور عشرة أيام على ميعاد استحقاقه فتصبح جميع أقساط العقد مستحقة الدفع فورا ودفعة واحدة، وللمؤجر أيضا الحق والخيار بفسخ هذا العقد واستلام المأجور ولو أن مدة الإجارة لم تنته كما وله الحق بوضع يده عليه وإجارته للغير بالبدل الذي يراه مناسبا على أن يعود بالفرق بين البدلين على المستأجر بحال نقصان البدل الثاني عن الأول.
<strong>تاسعا:</strong> بحال حدوث أمر من الأمرين المذكورين في البندين السابقين من هذا العقد فان للمؤجر الحق أيضا بوضع يده على أموال المستأجر الموجودة في المأجور وبيعها بالثمن الذي يراه مناسبا واستيفاء حقوقه من ثمنها.
<strong>عاشرا:</strong> للمؤجر الحق أن يبني طوابق علوية فوق المأجور أو بالقرب منه وأن يعمل جميع التصليحات والترميمات التي يريدها في المأجور وتوابعه أو بقريه مهما اقتضى لها من الوقت في مدة هذه الإجارة أو في المدة التي تمتد إليها ولا يجوز للمستأجر في ذلك الحال أن يطالب المؤجر بالتعويض عن أي عطل أو ضرر أو تنزيل في الأجرة بسبب هذه الأعمال
<strong>الحادي عشر:</strong> جميع ما يقوم به المستأجر من تحسينات أو تصليحات أو أعمال ديكور أو غيره تكون نفقتها عليه وحده وعند خروجه يكون المؤجر مخيرا إما بأخذها كما هي بدون مقابل أو بطلب إعادة المأجور كما ما كان عليه لحظة هذا العقد، وفي ذلك الحال تكون نفقات إعادة الحال وازالتها مهما بلغت على نفقة المستأجر وحده
<strong>الثاني عشر:</strong> لا يجوز للمستأجر أن يشغل العقار المستأجر لغير الغاية التي استأجر لها أو أن يستعمله فيما يخالف الشرع والقانون والنظام العام والآداب العامة، ولا يجوز له إحداث الضوضاء أو التسبب في الإزعاج للمجاورين
<strong>الثالث عشر:</strong> إذا كان المستأجرين في هذا العقد أكثر من شخص واحد فيعتبرون متكافلين ومتضامنين في كل ما ينشأ عنه من التزامات، وإذا كان المستأجر شركة أو شخص معنوي فان الشخص أو الأشخاص الذين يوقع و / أو يوقعون عن الشركة أو المؤسسة الشخص المعنوي) يعتبر و/ أو يعتبرون مسؤولا و / أو مسؤولين بالتكافل والتضامن معها بجميع مسؤوليات المستأجر في هذا العقد وما يترتب عليه من الالتزامات فيه طيلة مدة هذا العقد وأية مدد أخرى يتجدد إليها.
<strong>الرابع عشر:</strong> في حال رغب المؤجر انهاء العقد في نهاية مدته أو لم يرغب بتجديد العقد لمدة مماثلة فيعفى من توجيه الإنذار الذي يتطلبه قانون المالكين والمستأجرين ويجوز تقديم طلب مستعجل لإنهاء انتهاء المهلة التي القانون
<strong>الخامس عشر:</strong> لا يجوز للمستأجر أن يخالف أحكام البناء والتنظيم ويكون ملزما بتحمل أية مخالفة أو غرامة ناجمة عن مخالفة القوانين، أو الأنظمة، أو تعليمات البلديات، أو أحكام قانون الطوابق والشقق او امانة عمان وذلك عن طيلة فترة اشغاله للعقار.
<strong>السادس عشر:</strong> يلتزم المستأجر بنهاية مدة العقد بإحضار براءة ذمه للمؤجر من شركة الكهرباء وسلطة المياه والبلدية يثبت فيها عدم وجود اية مبالغ مترتبة على المأجور خلال فترة الإيجار
<strong>السابع عشر:</strong> للمؤجر الحق في تحديد أماكن وضع صحون الستالايت واللواقط الإلكترونية والإذاعية وخزانات الماء ولا يحق للمستأجر إضافة خزانات مياه إضافية بدون موافقة المؤجر الخطية مهما كانت غاية الإيجار.
<strong>الثامن عشر:</strong> اذا كان العقار المؤجر شقة فيلتزم المستأجر بأحكام قانون الملكية العقارية ونظام إدارة الشقق ويلتزم بدفع ما يترتب على الشقة من مستحقات تفرض على ادارة أو استعمال الخدمات المشتركة واذا كان للبناية حارس أو عامل نظافة فيلزم بدفع مستحقاته ويلتزم بدفع اية نفقات لصيانة الخدمات المشتركة بما فيها صيانة المصعد أو صيانة السطح حتى لو لم يكن يستخدمهما و يلتزم بدفع نسبته من فواتير المياه والكهرباء التي تستحق على الخدمات المشتركة، ولا يجوز له باي حال من الاحوال رفض المشاركة في مصاريف الخدمات المشتركة و لا يجوز له التذرع بعدم الاستفادة منها و يجب عليه أن يتقيد بالمكان المخصص الاصطفاف سيارته و لا يجوز له التعدي على الكراجات المخصصة لغيره من السكان
<strong>التاسع عشر:</strong> إن عدم احترام الجوار الساكنين في البناية التي تقع بها الشقة أو التي تقابلهم أو إيذاء أي من الجوار بأي أفعال لا يتقبلها العرف والعادة يعتبر سببا لفسخ العقد ويلزم المستأجر بالتعويض عن أي عطل أو ضرر يلحق بالمالك أو بالآخرين.
</div>'))
                        ->modalSubmitAction(false)
                ]),
     ])->columns(3),

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /// Signatures
Forms\Components\Section::make('توقيع المستأجر')
    ->schema([
    SignaturePad::make('tenant_signature')
    ->label('توقيع المستأجر')
    ->required()
    ->dehydrateStateUsing(function ($state, callable $set) {
        if ($state) {
            // حذف بادئة base64
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
            
            // اسم عشوائي للملف
            $fileName = 'signatures/' . Str::uuid() . '.png';

            // حفظ الملف في storage/app/public/signatures
            Storage::disk('public')->put($fileName, $imageData);

            // نحفظ فقط المسار
            $set('tenant_signature_path', $fileName);
        }

        return null; // لا نخزن base64
    }),
    // توقيع المؤجر
        SignaturePad::make('landlord_signature')
            ->label('توقيع المؤجر')
            ->required()
            ->dehydrateStateUsing(function ($state, callable $set) {
                if ($state) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
                    $fileName = 'signatures/' . Str::uuid() . '.png';
                    Storage::disk('public')->put($fileName, $imageData);
                    $set('landlord_signature_path', $fileName);
                }
                return null;
            }),

//        // توقيع الشاهد الأول
//        SignaturePad::make('witness1_signature')
//            ->label('توقيع الشاهد الأول')
//            ->required()
//            ->dehydrateStateUsing(function ($state, callable $set) {
//                if ($state) {
//                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
//                    $fileName = 'signatures/' . Str::uuid() . '.png';
//                    Storage::disk('public')->put($fileName, $imageData);
//                    $set('witness1_signature_path', $fileName);
//                }
//                return null;
//            }),
//
//        // توقيع الشاهد الثاني
//        SignaturePad::make('witness2_signature')
//            ->label('توقيع الشاهد الثاني')
//            ->required()
//            ->dehydrateStateUsing(function ($state, callable $set) {
//                if ($state) {
//                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
//                    $fileName = 'signatures/' . Str::uuid() . '.png';
//                    Storage::disk('public')->put($fileName, $imageData);
//                    $set('witness2_signature_path', $fileName);
//                }
//                return null;
//            }),
    ])->columns(4),
            



        // Pen color on export (defaults to penColor)
            Forms\Components\Section::make('Meta Info')->schema([
                Forms\Components\DatePicker::make('hired_date')
                    ->default(now())->readOnly(),
                Forms\Components\TextInput::make('hired_by')
                    ->default(fn () => Auth::user()?->name)
                    ->readOnly(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('landlord_name')
                    ->label('المؤجر')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ اسم المؤجر!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('tenant.firstname')
                    ->label('المستأجر')
                    ->searchable(['firstname', 'lastname'])
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->tenant ? $record->tenant->firstname . ' ' . $record->tenant->lastname : 'غير محدد')
                    ->copyable()
                    ->copyMessage('تم نسخ اسم المستأجر!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('tenant.phone')
                    ->label('هاتف المستأجر')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('تم نسخ رقم الهاتف!')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('property.name')
                    ->label('العقار')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('تم نسخ اسم العقار!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('الوحدة')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->copyMessage('تم نسخ اسم الوحدة!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit.rental_price')
                    ->label('سعر الإيجار')
                    ->sortable()
                    ->money('JOD')
                    ->alignEnd()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ البداية')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('end_date')
                    ->label('تاريخ النهاية')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contract_duration')
                    ->label('مدة العقد')
                    ->getStateUsing(function ($record) {
                        if ($record->start_date && $record->end_date) {
                            $start = \Carbon\Carbon::parse($record->start_date);
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $months = $start->diffInMonths($end);
                            return $months . ' شهر';
                        }
                        return 'غير محدد';
                    })
                    ->badge()
                    ->color('secondary')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'expired' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'expired' => 'منتهي الصلاحية',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('الأيام المتبقية')
                    ->getStateUsing(function ($record) {
                        if ($record->end_date) {
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $now = \Carbon\Carbon::now();
                            if ($end->isFuture()) {
                                return $now->diffInDays($end) . ' يوم';
                            }
                            return 'منتهي';
                        }
                        return 'غير محدد';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ($record->end_date) {
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $now = \Carbon\Carbon::now();
                            if ($end->isFuture()) {
                                $days = $now->diffInDays($end);
                                if ($days <= 30) return 'danger';
                                if ($days <= 90) return 'warning';
                                return 'success';
                            }
                        }
                        return 'gray';
                    })
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('has_signatures')
                    ->label('التوقيعات')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->tenant_signature_path) && !empty($record->landlord_signature_path))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('hired_by')
                    ->label('تم الإنشاء بواسطة')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('hired_date')
                    ->label('تاريخ الإنشاء')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('property_id')
                    ->label('العقار')
                    ->relationship('property', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('الوحدة')
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('المستأجر')
                    ->relationship('tenant', 'firstname')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'inactive' => 'غير نشط',
                        'expired' => 'منتهي الصلاحية',
                    ])
                    ->multiple(),
                    
                Tables\Filters\Filter::make('contract_dates')
                    ->label('تواريخ العقد')
                    ->form([
                        Forms\Components\DatePicker::make('start_date_from')
                            ->label('تاريخ البداية من'),
                        Forms\Components\DatePicker::make('start_date_until')
                            ->label('تاريخ البداية إلى'),
                        Forms\Components\DatePicker::make('end_date_from')
                            ->label('تاريخ النهاية من'),
                        Forms\Components\DatePicker::make('end_date_until')
                            ->label('تاريخ النهاية إلى'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['start_date_from'], fn ($q, $date) => $q->where('start_date', '>=', $date))
                            ->when($data['start_date_until'], fn ($q, $date) => $q->where('start_date', '<=', $date))
                            ->when($data['end_date_from'], fn ($q, $date) => $q->where('end_date', '>=', $date))
                            ->when($data['end_date_until'], fn ($q, $date) => $q->where('end_date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_date_from'] ?? null) {
                            $indicators['start_date_from'] = 'تاريخ البداية من: ' . \Carbon\Carbon::parse($data['start_date_from'])->format('Y-m-d');
                        }
                        if ($data['start_date_until'] ?? null) {
                            $indicators['start_date_until'] = 'تاريخ البداية إلى: ' . \Carbon\Carbon::parse($data['start_date_until'])->format('Y-m-d');
                        }
                        if ($data['end_date_from'] ?? null) {
                            $indicators['end_date_from'] = 'تاريخ النهاية من: ' . \Carbon\Carbon::parse($data['end_date_from'])->format('Y-m-d');
                        }
                        if ($data['end_date_until'] ?? null) {
                            $indicators['end_date_until'] = 'تاريخ النهاية إلى: ' . \Carbon\Carbon::parse($data['end_date_until'])->format('Y-m-d');
                        }
                        return $indicators;
                    }),
                    
                Tables\Filters\Filter::make('rental_price_range')
                    ->label('نطاق سعر الإيجار')
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->label('الحد الأدنى')
                            ->numeric()
                            ->suffix('JOD'),
                        Forms\Components\TextInput::make('max_price')
                            ->label('الحد الأقصى')
                            ->numeric()
                            ->suffix('JOD'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['min_price'], function ($q, $price) {
                                return $q->whereHas('unit', fn ($query) => $query->where('rental_price', '>=', $price));
                            })
                            ->when($data['max_price'], function ($q, $price) {
                                return $q->whereHas('unit', fn ($query) => $query->where('rental_price', '<=', $price));
                            });
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min_price'] ?? null) {
                            $indicators['min_price'] = 'الحد الأدنى للسعر: ' . number_format($data['min_price']) . ' JOD';
                        }
                        if ($data['max_price'] ?? null) {
                            $indicators['max_price'] = 'الحد الأقصى للسعر: ' . number_format($data['max_price']) . ' JOD';
                        }
                        return $indicators;
                    }),
                    
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('العقود المنتهية قريباً')
                    ->query(function ($query) {
                        return $query->where('end_date', '>=', now())
                                    ->where('end_date', '<=', now()->addDays(30));
                    })
                    ->toggle(),
                    
                Tables\Filters\Filter::make('with_signatures')
                    ->label('العقود مع التوقيعات')
                    ->query(function ($query) {
                        return $query->whereNotNull('tenant_signature_path')
                                    ->whereNotNull('landlord_signature_path');
                    })
                    ->toggle(),
                    
                Tables\Filters\Filter::make('created_this_month')
                    ->label('تم إنشاؤها هذا الشهر')
                    ->query(function ($query) {
                        return $query->whereBetween('created_at', [
                            now()->startOfMonth(),
                            now()->endOfMonth()
                        ]);
                    })
                    ->toggle(),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('تصدير العقود')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->fileName('contracts_' . date('Y-m-d'))
                    ->withColumns([
                        'id' => 'رقم العقد',
                        'landlord_name' => 'المؤجر',
                        'tenant.firstname' => 'اسم المستأجر الأول',
                        'tenant.lastname' => 'اسم المستأجر الأخير',
                        'tenant.phone' => 'هاتف المستأجر',
                        'tenant.email' => 'بريد المستأجر',
                        'property.name' => 'العقار',
                        'unit.name' => 'الوحدة',
                        'unit.rental_price' => 'سعر الإيجار',
                        'start_date' => 'تاريخ البداية',
                        'end_date' => 'تاريخ النهاية',
                        'status' => 'الحالة',
                        'hired_by' => 'تم الإنشاء بواسطة',
                        'hired_date' => 'تاريخ الإنشاء',
                        'created_at' => 'تاريخ الإضافة',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض')
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->label('تعديل')
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف المحددة')
                        ->color('danger'),
                    FilamentExportBulkAction::make('export-selected')
                        ->label('تصدير المحددة')
                        ->color('success')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->fileName('selected_contracts_' . date('Y-m-d'))
                        ->withColumns([
                            'id' => 'رقم العقد',
                            'landlord_name' => 'المؤجر',
                            'tenant.firstname' => 'اسم المستأجر الأول',
                            'tenant.lastname' => 'اسم المستأجر الأخير',
                            'tenant.phone' => 'هاتف المستأجر',
                            'property.name' => 'العقار',
                            'unit.name' => 'الوحدة',
                            'unit.rental_price' => 'سعر الإيجار',
                            'start_date' => 'تاريخ البداية',
                            'end_date' => 'تاريخ النهاية',
                            'status' => 'الحالة',
                            'created_at' => 'تاريخ الإضافة',
                        ]),
                ]),
            ])
            ->emptyStateHeading('لا توجد عقود')
            ->emptyStateDescription('ابدأ بإنشاء عقد جديد.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContract1s::route('/'),
            'create' => Pages\CreateContract1::route('/create'),
            'edit' => Pages\EditContract1::route('/{record}/edit'),
           //'view' => Pages\ViewContract1::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        return true;
    }

    public static function canView($record): bool
    {
        return true;
    }
}
