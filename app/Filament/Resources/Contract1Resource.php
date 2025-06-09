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
    protected static ?string $navigationGroup = 'Real Estate Management';
    protected static ?string $slug = 'contracts';

    public static function getNavigationLabel(): string
    {
        return __('general.Contracts');
    }
    
    public static function getModelLabel(): string
    {
        return __('general.Contract');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('general.Contracts');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            /// Landlord and Tenant Info
            Forms\Components\Section::make(__('general.Landlord and Tenant Information'))->schema([
                Forms\Components\TextInput::make('landlord_name')
                    ->label(__('general.Landlord Name'))
                    ->required(),

              Forms\Components\Select::make('tenant_id')
                    ->label(__('general.Tenant'))
                    ->relationship('tenant', 'firstname')
                    ->searchable()
                    ->required(),  
            ])->columns(2),

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /// Property & Unit        

            Forms\Components\Section::make(__('general.Property & Unit Details'))->schema([
                Forms\Components\Select::make('property_id')
                    ->options(Property::all()->pluck('name', 'id'))
                    ->required()
                    ->label(__('general.Property'))
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
                    ->label(__('general.Unit')),

                Forms\Components\TextInput::make('governorate')->label(__('general.Governorate'))->readOnly(),
                Forms\Components\TextInput::make('city')->label(__('general.City'))->readOnly(),
                Forms\Components\TextInput::make('district')->label(__('general.District'))->readOnly(),
                Forms\Components\TextInput::make('building_number')->label(__('general.Building Number'))->readOnly(),
                Forms\Components\TextInput::make('plot_number')->label(__('general.Plot Number'))->readOnly(),
                Forms\Components\TextInput::make('basin_number')->label(__('general.Basin Number'))->readOnly(),
                Forms\Components\TextInput::make('property_number')->label(__('general.Property Number'))->readOnly(),
                Forms\Components\TextInput::make('street_name')->label(__('general.Street Name'))->readOnly(),
            ])->columns(3),
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /// Contract Details
            Forms\Components\Section::make(__('general.Contract Details'))->schema([
                Forms\Components\DatePicker::make('start_date')
                    ->label(__('general.Start Date'))
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label(__('general.End Date'))
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->label(__('general.Due Date')),
                Forms\Components\TextInput::make('rent_amount')
                    ->label(__('general.Rent Amount'))
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label(__('general.Contract Status'))
                    ->options([
                        'active' => __('general.Active'), 
                        'inactive' => __('general.Inactive')
                    ])
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

 Forms\Components\Section::make(__('general.Terms and Conditions'))->schema([
                Forms\Components\Textarea::make('terms_and_conditions_extra')
                    ->label(__('general.Additional Terms and Conditions')),
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('show_terms')
                        ->label(__('general.Show Default Terms'))
                        ->color('success')
                        ->icon('heroicon-o-eye')
                        ->modalHeading(__('general.Default Terms and Conditions'))
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
Forms\Components\Section::make(__('general.Digital Signatures'))
    ->schema([
    SignaturePad::make('tenant_signature_path')
    ->label(__('general.Tenant Signature'))
    ->required()
    ->exportPenColor('#007bff')
    ->dehydrateStateUsing(function ($state, callable $set) {
        if ($state) {
            // Remove base64 prefix
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
            
            // Generate random filename
            $fileName = 'contracts/signatures/' . Str::uuid() . '.png';

            // Ensure directory exists
            $directory = public_path('uploads/contracts/signatures');
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save file to public/uploads/contracts/signatures
            $publicPath = public_path('uploads/' . $fileName);
            file_put_contents($publicPath, $imageData);

            // Return the filename to be saved in database
            return $fileName;
        }

        return null;
    }),
    // Landlord signature
        SignaturePad::make('landlord_signature_path')
            ->label(__('general.Landlord Signature'))
            ->required()
            ->exportPenColor('#007bff')
            ->dehydrateStateUsing(function ($state, callable $set) {
                if ($state) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
                    $fileName = 'contracts/signatures/' . Str::uuid() . '.png';
                    
                    // Ensure directory exists
                    $directory = public_path('uploads/contracts/signatures');
                    if (!is_dir($directory)) {
                        mkdir($directory, 0755, true);
                    }
                    
                    $publicPath = public_path('uploads/' . $fileName);
                    file_put_contents($publicPath, $imageData);
                    
                    // Return the filename to be saved in database
                    return $fileName;
                }
                return null;
            }),

        // First Witness Signature
        SignaturePad::make('witness1_signature_path')
            ->label(__('general.First Witness Signature'))
            ->required()
            ->exportPenColor('#007bff')
            ->dehydrateStateUsing(function ($state, callable $set) {
                if ($state) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
                    $fileName = 'contracts/signatures/' . Str::uuid() . '.png';
                    
                    // Ensure directory exists
                    $directory = public_path('uploads/contracts/signatures');
                    if (!is_dir($directory)) {
                        mkdir($directory, 0755, true);
                    }
                    
                    $publicPath = public_path('uploads/' . $fileName);
                    file_put_contents($publicPath, $imageData);
                    
                    // Return the filename to be saved in database
                    return $fileName;
                }
                return null;
            }),

        // First Witness Name
        Forms\Components\TextInput::make('witness1_name')
            ->label(__('general.First Witness Name'))
            ->maxLength(255),

        // Second Witness Signature
        SignaturePad::make('witness2_signature_path')
            ->label(__('general.Second Witness Signature'))
            ->required()
            ->exportPenColor('#007bff')
            ->dehydrateStateUsing(function ($state, callable $set) {
                if ($state) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
                    $fileName = 'contracts/signatures/' . Str::uuid() . '.png';
                    
                    // Ensure directory exists
                    $directory = public_path('uploads/contracts/signatures');
                    if (!is_dir($directory)) {
                        mkdir($directory, 0755, true);
                    }
                    
                    $publicPath = public_path('uploads/' . $fileName);
                    file_put_contents($publicPath, $imageData);
                    
                    // Return the filename to be saved in database
                    return $fileName;
                }
                return null;
            }),

        // Second Witness Name
        Forms\Components\TextInput::make('witness2_name')
            ->label(__('general.Second Witness Name'))
            ->maxLength(255),
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
                    ->label(__('general.Contract ID'))
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('landlord_name')
                    ->label(__('general.Landlord'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('general.Landlord name copied!'))
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('tenant.firstname')
                    ->label(__('general.Tenant'))
                    ->searchable(['firstname', 'lastname'])
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->tenant ? $record->tenant->firstname . ' ' . $record->tenant->lastname : __('general.Not specified'))
                    ->copyable()
                    ->copyMessage(__('general.Tenant name copied!'))
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('tenant.phone')
                    ->label(__('general.Tenant Phone'))
                    ->searchable()
                    ->copyable()
                    ->copyMessage(__('general.Phone number copied!'))
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('property.name')
                    ->label(__('general.Property'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('general.Property name copied!'))
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit.name')
                    ->label(__('general.Unit'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->copyMessage(__('general.Unit name copied!'))
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit.rental_price')
                    ->label(__('general.Rental Price'))
                    ->sortable()
                    ->money('JOD')
                    ->alignEnd()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('general.Start Date'))
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('general.End Date'))
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contract_duration')
                    ->label(__('general.Contract Duration'))
                    ->getStateUsing(function ($record) {
                        if ($record->start_date && $record->end_date) {
                            $start = \Carbon\Carbon::parse($record->start_date);
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $months = $start->diffInMonths($end);
                            return $months . ' ' . __('general.months');
                        }
                        return __('general.Not specified');
                    })
                    ->badge()
                    ->color('secondary')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label(__('general.Status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'expired' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => __('general.Active'),
                        'inactive' => __('general.Inactive'),
                        'expired' => __('general.Expired'),
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label(__('general.Days Remaining'))
                    ->getStateUsing(function ($record) {
                        if ($record->end_date) {
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $now = \Carbon\Carbon::now();
                            if ($end->isFuture()) {
                                return $now->diffInDays($end) . ' ' . __('general.days');
                            }
                            return __('general.Expired');
                        }
                        return __('general.Not specified');
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
                    ->label(__('general.Signatures'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->tenant_signature_path) && !empty($record->landlord_signature_path))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('has_pdf')
                    ->label(__('general.PDF'))
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->hasPdf())
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-document-minus')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('hired_by')
                    ->label(__('general.Created By'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('hired_date')
                    ->label(__('general.Created Date'))
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('general.Date Added'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('property_id')
                    ->label(__('general.Property'))
                    ->relationship('property', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('unit_id')
                    ->label(__('general.Unit'))
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label(__('general.Tenant'))
                    ->relationship('tenant', 'firstname')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('general.Status'))
                    ->options([
                        'active' => __('general.Active'),
                        'inactive' => __('general.Inactive'),
                        'expired' => __('general.Expired'),
                    ])
                    ->multiple(),
                    
                Tables\Filters\Filter::make('contract_dates')
                    ->label(__('general.Contract Dates'))
                    ->form([
                        Forms\Components\DatePicker::make('start_date_from')
                            ->label(__('general.Start Date From')),
                        Forms\Components\DatePicker::make('start_date_until')
                            ->label(__('general.Start Date Until')),
                        Forms\Components\DatePicker::make('end_date_from')
                            ->label(__('general.End Date From')),
                        Forms\Components\DatePicker::make('end_date_until')
                            ->label(__('general.End Date Until')),
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
                    ->label(__('general.Rental Price Range'))
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->label(__('general.Minimum Price'))
                            ->numeric()
                            ->suffix('JOD'),
                        Forms\Components\TextInput::make('max_price')
                            ->label(__('general.Maximum Price'))
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
                    ->label(__('general.Contracts Expiring Soon'))
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
                    ->label(__('general.View'))
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->label(__('general.Edit'))
                    ->color('warning'),
                    
                // View PDF Action
                Tables\Actions\Action::make('view_pdf')
                    ->label(__('general.View PDF'))
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn (Contract1 $record): string => $record->pdf_url ?? '#')
                    ->openUrlInNewTab()
                    ->visible(fn (Contract1 $record): bool => $record->hasPdf()),
                    
                // Generate/Regenerate PDF Action
                Tables\Actions\Action::make('generate_pdf')
                    ->label(fn (Contract1 $record): string => $record->hasPdf() ? __('general.Regenerate PDF') : __('general.Generate PDF'))
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function (Contract1 $record) {
                        $contractPdfService = new \App\Services\ContractPdfService();
                        $pdfPath = $contractPdfService->regenerateContractPdf($record);
                        
                        if ($pdfPath) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('general.PDF Generated Successfully'))
                                ->body(__('general.Contract PDF has been generated and saved.'))
                                ->success()
                                ->duration(5000)
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title(__('general.PDF Generation Failed'))
                                ->body(__('general.There was an error generating the PDF. Please try again.'))
                                ->danger()
                                ->duration(7000)
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading(__('general.Generate Contract PDF'))
                    ->modalDescription(__('general.This will generate a PDF version of the contract. Are you sure?')),
                    
                Tables\Actions\DeleteAction::make()
                    ->label(__('general.Delete'))
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('general.Delete Selected'))
                        ->color('danger'),
                    FilamentExportBulkAction::make('export-selected')
                        ->label(__('general.Export Selected')),
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