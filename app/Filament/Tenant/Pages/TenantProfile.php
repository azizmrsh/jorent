<?php

namespace App\Filament\Tenant\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class TenantProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    
    protected static string $view = 'filament.tenant.pages.tenant-profile';
    
    protected static ?string $title = 'الملف الشخصي';
    
    protected static ?string $navigationLabel = 'الملف الشخصي';
    
    protected static ?string $navigationGroup = 'الملف الشخصي';

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = auth('tenant')->user();
        
        $this->form->fill([
            'firstname' => $tenant->firstname,
            'midname' => $tenant->midname,
            'lastname' => $tenant->lastname,
            'email' => $tenant->email,
            'phone' => $tenant->phone,
            'address' => $tenant->address,
            'birth_date' => $tenant->birth_date,
            'nationality' => $tenant->nationality,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('المعلومات الشخصية')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('firstname')
                                    ->label('الاسم الأول')
                                    ->required()
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('midname')
                                    ->label('الاسم الأوسط')
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('lastname')
                                    ->label('اسم العائلة')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                    
                                Forms\Components\TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel()
                                    ->maxLength(255),
                            ]),
                            
                        Forms\Components\Textarea::make('address')
                            ->label('العنوان')
                            ->rows(3)
                            ->columnSpanFull(),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('birth_date')
                                    ->label('تاريخ الميلاد'),
                                    
                                Forms\Components\TextInput::make('nationality')
                                    ->label('الجنسية')
                                    ->maxLength(255),
                            ]),
                    ]),
                    
                Forms\Components\Section::make('تغيير كلمة المرور')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('كلمة المرور الحالية')
                            ->password()
                            ->required()
                            ->rule('current_password:tenant'),
                            
                        Forms\Components\TextInput::make('password')
                            ->label('كلمة المرور الجديدة')
                            ->password()
                            ->required()
                            ->confirmed()
                            ->minLength(8),
                            
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('تأكيد كلمة المرور الجديدة')
                            ->password()
                            ->required(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('حفظ التغييرات')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        $tenant = auth('tenant')->user();
        
        // تحديث البيانات الأساسية
        $tenant->update([
            'firstname' => $data['firstname'],
            'midname' => $data['midname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'birth_date' => $data['birth_date'],
            'nationality' => $data['nationality'],
        ]);
        
        // تحديث كلمة المرور إذا تم إدخالها
        if (!empty($data['password'])) {
            $tenant->update([
                'password' => Hash::make($data['password']),
            ]);
        }
        
        Notification::make()
            ->title('تم حفظ التغييرات بنجاح')
            ->success()
            ->send();
    }
}
