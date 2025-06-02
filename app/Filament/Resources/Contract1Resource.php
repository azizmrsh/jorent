<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Contract1Resource\Pages;
use App\Models\Contract1;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use App\Traits\FileUploadTrait;
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
use Carbon\Carbon;
use Illuminate\Support\HtmlString;

//osaid 
// Export functionality imports
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;



class Contract1Resource extends Resource
{
    use FileUploadTrait;
    
    protected static ?string $model = Contract1::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Rental Management';
    protected static ?string $navigationLabel = 'Contracts';
    protected static ?string $slug = 'contracts';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([

            /// Landlord and Tenant Info
            Forms\Components\Section::make('Landlord and Tenant Information')->schema([
                Forms\Components\TextInput::make('landlord_name')
                    ->label('Landlord Name')
                    ->required(),

              Forms\Components\Select::make('tenant_id')
                    ->label('Tenant')
                    ->relationship('tenant', 'firstname')
                    ->searchable()
                    ->required(),  
            ])->columns(2),

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /// Property & Unit        

            Forms\Components\Section::make('Property & Unit Details')->schema([
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
                Forms\Components\DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label('End Date')
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Due Date'),
                Forms\Components\TextInput::make('rent_amount')
                    ->label('Rent Amount')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Contract Status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->reactive()
                    ->afterStateHydrated(function (callable $set, callable $get) {
                        $startDate = $get('start_date');
                        $endDate = $get('end_date');

                        if (
                            $startDate && $endDate &&
                            Carbon::parse($startDate)->lte(now()) &&
                            Carbon::parse($endDate)->gte(now())
                        ) {
                            $set('status', 'active');
                        } else {
                            $set('status', 'inactive');
                        }
                    }),
            ])->columns(3),

 Forms\Components\Section::make('Terms and Conditions')->schema([
                Forms\Components\Textarea::make('terms_and_conditions_extra')
                    ->label('Additional Terms and Conditions'),
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('show_terms')
                        ->label('عرض الشروط الافتراضية')
                        ->color('success')
                        ->icon('heroicon-o-eye')
                        ->modalHeading('الشروط والأحكام الافتراضية')
                        ->modalContent(fn() => new \Illuminate\Support\HtmlString('
                            <div style="
                                direction: rtl;
                                text-align: right;
                                font-size: 1.1rem;
                                background: linear-gradient(135deg, #f8fafc 0%, #e0e7ef 100%);
                                border-radius: 16px;
                                box-shadow: 0 2px 12px rgba(0,0,0,0.07);
                                padding: 32px 24px;
                                margin: 0 auto;
                                max-width: 800px;
                                border: 1px solid #e5e7eb;
                                line-height: 2.1;
                            ">
                                <h2 style="color:#2563eb;font-weight:bold;font-size:1.3rem;margin-bottom:18px;text-align:center;">
                                    الشروط والأحكام الافتراضية لعقد الإيجار
                                </h2>
                                <ol style="padding-right: 18px;">
                                    <li><strong>أولاً:</strong> تعتبر مقدمة هذا العقد وشروطه وملحقاته إن وجدت جزءاً لا يتجزأ منه وتقرأ معه كوحدة واحدة.</li>
                                    <li><strong>ثانياً:</strong> يقر المستأجر بأنه قد استلم المأجور وملحقاته سالماً من كل عيب وقد عاين بنفسه كافة الأبواب والشبابيك والزجاج والغالات بمفاتيحها والمغاسل والحنفيات والأدوات الصحية والدهان والبلاط والسيراميك والجبصين وكامل الديكورات وأن جميع هذه الأشياء والتوابع جديدة وسليمة وخالية من أي عيب أو خلل ويتعهد المستأجر بتسليمها عند انتهاء مدة الإجارة جديدة بالحالة التي استلمها بها.</li>
                                    <li><strong>ثالثاً:</strong> يجب على المستأجر قبل انتهاء مدة العقد إذا كان لا يرغب بالتجديد لمدة مماثلة أن يقوم بتبليغ المؤجر قبل انتهاء مدة العقد بشهرين على الأقل وإلا يعتبر مستأجراً للعقار لمدة مماثلة أخرى إذا أراد المؤجر ذلك، مع التأكيد على عدم انطباق هذا الشرط على المؤجر.</li>
                                    <li><strong>رابعاً:</strong> لا يجوز للمستأجر تأجير المأجور أو جزء منه للغير أو إدخال شريك أو شركة معه في المأجور أو التخلي عنه كلياً أو جزئياً للغير بدون موافقة المؤجر الخطية.</li>
                                    <li><strong>خامساً:</strong> لا يحق للمستأجر أن يحدث أي تغيير في المأجور من هدم، أو بناء، أو فتح شبابيك، أو إحداث سدة، أو إحداث أي تغيير في الأبواب أو الحنفيات أو ثقب الجدران وغيرها إلا بموافقة المؤجر الخطية، وفي كل الأحوال على أن يقوم بإعادتها على نفقته إلى الحالة التي استلمها عليه عند توقيعه للعقد، ويجب على المستأجر إعادة أي ملحقات استلمها مع المأجور بالحالة التي استلمها بها.</li>
                                    <li><strong>سادساً:</strong> كل ما يحصل في المأجور من عطل، أو عيب، أو خراب أو تلف في المجاري، أو التمديدات الصحية، أو الكهربائية، أو القصارة، أو التشطيبات، أو أي من المرافق الملحقة بالمأجور فيعود تصليحها على المستأجر ولا يحق له أن يطالب المؤجر بشيء من التعويضات كما لا يحق له أن يطالب المؤجر بأي تعويضات أو ضرر أو عطل مهما كان نوعه بسبب أي تعطيل أو خلل يحصل في الخدمات المشتركة الملحقة بالعمارة.</li>
                                    <li><strong>سابعاً:</strong> يلتزم المستأجر بدفع كافة الرسوم والمصاريف والنفقات والفواتير المفروضة على المأجور بما فيها أجور الحراسة والنظافة والكهرباء والهاتف وضريبة المسقفات وضريبة المعارف بالإضافة إلى كافة نفقات الصيانة وغيرها.</li>
                                    <li><strong>ثامناً:</strong> إذا امتنع أو تأخر المستأجر عن دفع أي قسط من أقساط بدل الإيجار بعد مرور عشرة أيام على ميعاد استحقاقه فتصبح جميع أقساط العقد مستحقة الدفع فوراً ودفعة واحدة، وللمؤجر أيضاً الحق والخيار بفسخ هذا العقد واستلام المأجور ولو أن مدة الإجارة لم تنته كما وله الحق بوضع يده عليه وإجارته للغير بالبدل الذي يراه مناسباً على أن يعود بالفرق بين البدلين على المستأجر بحال نقصان البدل الثاني عن الأول.</li>
                                    <li><strong>تاسعاً:</strong> بحال حدوث أمر من الأمرين المذكورين في البندين السابقين من هذا العقد فإن للمؤجر الحق أيضاً بوضع يده على أموال المستأجر الموجودة في المأجور وبيعها بالثمن الذي يراه مناسباً واستيفاء حقوقه من ثمنها.</li>
                                    <li><strong>عاشراً:</strong> للمؤجر الحق أن يبني طوابق علوية فوق المأجور أو بالقرب منه وأن يعمل جميع التصليحات والترميمات التي يريدها في المأجور وتوابعه أو بقربه مهما اقتضى لها من الوقت في مدة هذه الإجارة أو في المدة التي تمتد إليها ولا يجوز للمستأجر في ذلك الحال أن يطالب المؤجر بالتعويض عن أي عطل أو ضرر أو تنزيل في الأجرة بسبب هذه الأعمال.</li>
                                    <li><strong>الحادي عشر:</strong> جميع ما يقوم به المستأجر من تحسينات أو تصليحات، أو أعمال ديكور أو غيره تكون نفقتها عليه وحده وعند خروجه يكون المؤجر مخيراً إما بأخذها كما هي بدون مقابل أو بطلب إعادة المأجور كما كان عليه لحظة هذا العقد، وفي ذلك الحال تكون نفقات إعادة الحال وإزالتها مهما بلغت على نفقة المستأجر وحده.</li>
                                    <li><strong>الثاني عشر:</strong> لا يجوز للمستأجر أن يشغل العقار المستأجر لغير الغاية التي استأجر لها أو أن يستعمله فيما يخالف الشرع والقانون والنظام العام والآداب العامة، ولا يجوز له إحداث الضوضاء أو التسبب في الإزعاج للمجاورين.</li>
                                    <li><strong>الثالث عشر:</strong> إذا كان المستأجرين في هذا العقد أكثر من شخص واحد فيعتبرون متكافلين ومتضامنين في كل ما ينشأ عنه من التزامات، وإذا كان المستأجر شركة أو شخصاً معنوياً فإن الشخص أو الأشخاص الذين يوقع و/أو يوقعون عن الشركة أو المؤسسة (الشخص المعنوي) يعتبر و/أو يعتبرون مسؤولاً و/أو مسؤولين بالتكافل والتضامن معها بجميع مسؤوليات المستأجر في هذا العقد وما يترتب عليه من الالتزامات فيه طيلة مدة هذا العقد وأية مدد أخرى يتجدد إليها.</li>
                                    <li><strong>الرابع عشر:</strong> في حال رغب المؤجر إنهاء العقد في نهاية مدته أو لم يرغب بتجديد العقد لمدة مماثلة فيُعفى من توجيه الإنذار الذي يتطلبه قانون المالكين والمستأجرين ويجوز تقديم طلب مستعجل لإنهاء العقد مباشرة بعد انتهاء المهلة التي حددها القانون.</li>
                                    <li><strong>الخامس عشر:</strong> لا يجوز للمستأجر أن يخالف أحكام البناء والتنظيم ويكون ملزماً بتحمل أية مخالفة أو غرامة ناجمة عن مخالفة القوانين أو الأنظمة أو تعليمات البلديات أو أحكام قانون الطوابق والشقق أو أمانة عمان وذلك عن طيلة فترة إشغاله للعقار.</li>
                                    <li><strong>السادس عشر:</strong> يلتزم المستأجر بنهاية مدة العقد بإحضار براءة ذمة للمؤجر من شركة الكهرباء وسلطة المياه والبلدية يثبت فيها عدم وجود أية مبالغ مترتبة على المأجور خلال فترة الإيجار.</li>
                                    <li><strong>السابع عشر:</strong> للمؤجر الحق في تحديد أماكن وضع صحون الستالايت واللواقط الإلكترونية والإذاعية وخزانات الماء ولا يحق للمستأجر إضافة خزانات مياه إضافية بدون موافقة المؤجر الخطية مهما كانت غاية الإيجار.</li>
                                    <li><strong>الثامن عشر:</strong> إذا كان العقار المؤجر شقة فيلتزم المستأجر بأحكام قانون الملكية العقارية ونظام إدارة الشقق ويلتزم بدفع ما يترتب على الشقة من مستحقات تفرض على إدارة أو استعمال الخدمات المشتركة وإذا كان للبناية حارس أو عامل نظافة فيلزم بدفع مستحقاته ويلتزم بدفع أية نفقات صيانة الخدمات المشتركة بما فيها صيانة المصعد أو صيانة السطح حتى لو لم يكن يستخدمهما ويلتزم بدفع نسبته من فواتير المياه والكهرباء التي تستحق على الخدمات المشتركة، ولا يجوز له بأي حال من الأحوال رفض المشاركة في مصاريف الخدمات المشتركة ولا يجوز له التذرع بعدم الاستفادة منها ويجب عليه أن يتقيد بالمكان المخصص لاصطفاف سيارته ولا يجوز له التعدي على الكراجات المخصصة لغيره من السكان.</li>
                                    <li><strong>التاسع عشر:</strong> إن عدم احترام الجوار الساكنين في البناية التي تقع بها الشقة أو التي تقابلهم أو إيذاء أي من الجوار بأي أفعال لا يتقبلها العرف والعادة يعتبر سبباً لفسخ العقد ويلزم المستأجر بالتعويض عن أي عطل أو ضرر يلحق بالمالك أو بالآخرين.</li>
                                </ol>
                            </div>
                        '))
                        ->modalSubmitAction(false)

                ]),
     ])->columns(3),

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /// Signatures
Forms\Components\Section::make('Digital Signatures')
    ->schema([
    SignaturePad::make('tenant_signature')
    ->label('Tenant Signature')
    ->required()
    ->dehydrateStateUsing(function ($state, callable $set) {
        if ($state) {
            // Remove base64 prefix
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
            
            // Generate random filename
            $fileName = 'contracts/signatures/' . Str::uuid() . '.png';

            // Save file to public/uploads/contracts/signatures
            $publicPath = public_path('uploads/' . $fileName);
            file_put_contents($publicPath, $imageData);

            // Save only the path
            $set('tenant_signature_path', $fileName);
        }

        return null; // Don't store base64
    }),
    // Landlord signature
        SignaturePad::make('landlord_signature')
            ->label('Landlord Signature')
            ->required()
            ->dehydrateStateUsing(function ($state, callable $set) {
                if ($state) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
                    $fileName = 'contracts/signatures/' . Str::uuid() . '.png';
                    $publicPath = public_path('uploads/' . $fileName);
                    file_put_contents($publicPath, $imageData);
                    $set('landlord_signature_path', $fileName);
                }
                return null;
            }),

//        // First Witness Signature
//        SignaturePad::make('witness1_signature')
//            ->label('First Witness Signature')
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
//        // Second Witness Signature
//        SignaturePad::make('witness2_signature')
//            ->label('Second Witness Signature')
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
            Forms\Components\Section::make('Meta Information')->schema([
                Forms\Components\DatePicker::make('hired_date')
                    ->label('Created Date')
                    ->default(now())
                    ->readOnly(),
                Forms\Components\TextInput::make('hired_by')
                    ->label('Created By')
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
                    ->label('Contract ID')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('landlord_name')
                    ->label('Landlord')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Landlord name copied!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('tenant.firstname')
                    ->label('Tenant')
                    ->searchable(['firstname', 'lastname'])
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->tenant ? $record->tenant->firstname . ' ' . $record->tenant->lastname : 'Not specified')
                    ->copyable()
                    ->copyMessage('Tenant name copied!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('tenant.phone')
                    ->label('Tenant Phone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone number copied!')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('property.name')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Property name copied!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->copyMessage('Unit name copied!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit.rental_price')
                    ->label('Rental Price')
                    ->sortable()
                    ->money('JOD')
                    ->alignEnd()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contract_duration')
                    ->label('Contract Duration')
                    ->getStateUsing(function ($record) {
                        if ($record->start_date && $record->end_date) {
                            $start = \Carbon\Carbon::parse($record->start_date);
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $months = $start->diffInMonths($end);
                            return $months . ' months';
                        }
                        return 'Not specified';
                    })
                    ->badge()
                    ->color('secondary')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'expired' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'expired' => 'Expired',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Days Remaining')
                    ->getStateUsing(function ($record) {
                        if ($record->end_date) {
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $now = \Carbon\Carbon::now();
                            if ($end->isFuture()) {
                                return $now->diffInDays($end) . ' days';
                            }
                            return 'Expired';
                        }
                        return 'Not specified';
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
                    ->label('Signatures')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->tenant_signature_path) && !empty($record->landlord_signature_path))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('has_pdf')
                    ->label('PDF')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->hasPdf())
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-document-minus')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('hired_by')
                    ->label('Created By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('hired_date')
                    ->label('Created Date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Added')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('property_id')
                    ->label('Property')
                    ->relationship('property', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('Tenant')
                    ->relationship('tenant', 'firstname')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'expired' => 'Expired',
                    ])
                    ->multiple(),
                    
                Tables\Filters\Filter::make('contract_dates')
                    ->label('Contract Dates')
                    ->form([
                        Forms\Components\DatePicker::make('start_date_from')
                            ->label('Start Date From'),
                        Forms\Components\DatePicker::make('start_date_until')
                            ->label('Start Date Until'),
                        Forms\Components\DatePicker::make('end_date_from')
                            ->label('End Date From'),
                        Forms\Components\DatePicker::make('end_date_until')
                            ->label('End Date Until'),
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
                            $indicators['start_date_from'] = 'Start Date From: ' . \Carbon\Carbon::parse($data['start_date_from'])->format('Y-m-d');
                        }
                        if ($data['start_date_until'] ?? null) {
                            $indicators['start_date_until'] = 'Start Date Until: ' . \Carbon\Carbon::parse($data['start_date_until'])->format('Y-m-d');
                        }
                        if ($data['end_date_from'] ?? null) {
                            $indicators['end_date_from'] = 'End Date From: ' . \Carbon\Carbon::parse($data['end_date_from'])->format('Y-m-d');
                        }
                        if ($data['end_date_until'] ?? null) {
                            $indicators['end_date_until'] = 'End Date Until: ' . \Carbon\Carbon::parse($data['end_date_until'])->format('Y-m-d');
                        }
                        return $indicators;
                    }),
                    
                Tables\Filters\Filter::make('rental_price_range')
                    ->label('Rental Price Range')
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->label('Minimum Price')
                            ->numeric()
                            ->suffix('JOD'),
                        Forms\Components\TextInput::make('max_price')
                            ->label('Maximum Price')
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
                            $indicators['min_price'] = 'Minimum Price: ' . number_format($data['min_price']) . ' JOD';
                        }
                        if ($data['max_price'] ?? null) {
                            $indicators['max_price'] = 'Maximum Price: ' . number_format($data['max_price']) . ' JOD';
                        }
                        return $indicators;
                    }),
                    
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Contracts Expiring Soon')
                    ->query(function ($query) {
                        return $query->where('end_date', '>=', now())
                                    ->where('end_date', '<=', now()->addDays(30));
                    })
                    ->toggle(),
                    
                Tables\Filters\Filter::make('with_signatures')
                    ->label('Contracts with Signatures')
                    ->query(function ($query) {
                        return $query->whereNotNull('tenant_signature_path')
                                    ->whereNotNull('landlord_signature_path');
                    })
                    ->toggle(),
                    

            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                   ->label('Export Contracts'),
                //    ->color('success')
                //    ->icon('heroicon-o-arrow-down-tray')
                //    ->fileName('contracts_' . date('Y-m-d'))
                //    ->withColumns([
                //        'id' => 'Contract ID',
                //        'landlord_name' => 'Landlord',
                //        'tenant_name' => 'Tenant Name',
                //        'tenant_phone' => 'Tenant Phone',
                //        'tenant_email' => 'Tenant Email',
                //        'property_name' => 'Property',
                //        'unit_name' => 'Unit',
                //        'rental_price' => 'Rental Price',
                //        'start_date' => 'Start Date',
                //        'end_date' => 'End Date',
                //        'status' => 'Status',
                //        'hired_by' => 'Created By',
                //        'hired_date' => 'Creation Date',
                //        'created_at' => 'Added Date',
                //    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->color('warning'),
                    
                // View PDF Action
                Tables\Actions\Action::make('view_pdf')
                    ->label('View PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn (Contract1 $record): string => $record->pdf_url ?? '#')
                    ->openUrlInNewTab()
                    ->visible(fn (Contract1 $record): bool => $record->hasPdf()),
                    
                // Generate/Regenerate PDF Action
                Tables\Actions\Action::make('generate_pdf')
                    ->label(fn (Contract1 $record): string => $record->hasPdf() ? 'Regenerate PDF' : 'Generate PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (Contract1 $record) {
                        $contractPdfService = new \App\Services\ContractPdfService();
                        $pdfPath = $contractPdfService->regenerateContractPdf($record);
                        
                        if ($pdfPath) {
                            \Filament\Notifications\Notification::make()
                                ->title('PDF Generated Successfully')
                                ->body('Contract PDF has been generated and saved.')
                                ->success()
                                ->duration(5000)
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('PDF Generation Failed')
                                ->body('There was an error generating the PDF. Please try again.')
                                ->danger()
                                ->duration(7000)
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate Contract PDF')
                    ->modalDescription('This will generate a PDF version of the contract. Are you sure?'),
                    
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->color('danger'),
                    FilamentExportBulkAction::make('export-selected')
                        ->label('Export Selected'),
                    //    ->color('success')
                    //    ->icon('heroicon-o-arrow-down-tray')
                    //    ->fileName('selected_contracts_' . date('Y-m-d'))
                    //    ->withColumns([
                    //        'id' => 'Contract ID',
                    //        'landlord_name' => 'Landlord',
                    //        'tenant_name' => 'Tenant Name',
                    //        'tenant_phone' => 'Tenant Phone',
                    //        'property_name' => 'Property',
                    //        'unit_name' => 'Unit',
                    //        'rental_price' => 'Rental Price',
                    //        'start_date' => 'Start Date',
                    //        'end_date' => 'End Date',
                    //        'status' => 'Status',
                    //        'created_at' => 'Added Date',
                    //    ]),
                ]),
            ])
            ->emptyStateHeading('No Contracts Found')
            ->emptyStateDescription('Start by creating a new contract.')
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
