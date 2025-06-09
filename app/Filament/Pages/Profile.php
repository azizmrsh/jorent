<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Support\Enums\MaxWidth;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;
    
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = null;
    protected static ?string $title = null;
    
    public static function getNavigationLabel(): string
    {
        return __('general.My Profile');
    }
    
    public function getTitle(): string
    {
        return __('general.Profile Settings');
    }
    protected static ?string $slug = 'profile';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.profile';
    protected static ?string $navigationGroup = 'Settings';    public ?array $data = [];

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FiveExtraLarge;
    }    public function mount(): void
    {
        $user = Auth::user();
        
        $this->form->fill([
            'name' => $user->name,
            'midname' => $user->midname,
            'lastname' => $user->lastname,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'birth_date' => $user->birth_date,
            'profile_photo' => $user->profile_photo,
        ]);
        
        // Log profile access for security
        \Log::info('Profile accessed', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);
    }public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Profile Management')
                    ->tabs([
                        // Personal Information Tab
                        Tabs\Tab::make('Personal Information')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Basic Information')
                                    ->description('Update your personal details')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('name')
                                                ->label('First Name')
                                                ->required()
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('midname')
                                                ->label('Middle Name')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('lastname')
                                                ->label('Last Name')
                                                ->required()
                                                ->maxLength(255),
                                        ]),
                                        
                                        Grid::make(2)->schema([
                                            Forms\Components\DatePicker::make('birth_date')
                                                ->label('Birth Date')
                                                ->displayFormat('Y-m-d')
                                                ->maxDate(now()->subYears(16)), // Must be at least 16 years old
                                            Forms\Components\TextInput::make('phone')
                                                ->label('Phone Number')
                                                ->tel()
                                                ->maxLength(255),
                                        ]),
                                        
                                        Forms\Components\Textarea::make('address')
                                            ->label('Address')
                                            ->rows(3)
                                            ->maxLength(500),
                                    ]),
                            ]),

                        // Contact Information Tab
                        Tabs\Tab::make('Contact & Security')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                Section::make('Contact Information')
                                    ->description('Manage your contact details')
                                    ->schema([
                                        Forms\Components\TextInput::make('email')
                                            ->label('Email Address')
                                            ->email()
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->rule('email')
                                            ->helperText('This email will be used for system notifications')
                                            ->prefixIcon('heroicon-o-envelope')
                                            ->suffixAction(
                                                Forms\Components\Actions\Action::make('verifyEmail')
                                                    ->icon('heroicon-o-check-badge')
                                                    ->color('success')
                                                    ->tooltip('Email verification status')
                                                    ->disabled()
                                                    ->visible(fn () => Auth::user()->email_verified_at !== null)
                                            ),
                                    ]),
                                
                                Section::make('Password Change')
                                    ->description('Update your account password')
                                    ->schema([
                                        Forms\Components\TextInput::make('current_password')
                                            ->label('Current Password')
                                            ->password()
                                            ->currentPassword()
                                            ->validationMessages([
                                                'current_password' => 'The current password is incorrect.',
                                            ]),
                                        
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('new_password')
                                                ->label('New Password')
                                                ->password()
                                                ->rule(Password::default())
                                                ->same('new_password_confirmation')
                                                ->validationMessages([
                                                    'same' => 'The password confirmation does not match.',
                                                ]),
                                            Forms\Components\TextInput::make('new_password_confirmation')
                                                ->label('Confirm New Password')
                                                ->password(),
                                        ]),
                                    ]),
                            ]),

                        // Profile Photo Tab
                        Tabs\Tab::make('Profile Photo')
                            ->icon('heroicon-o-camera')
                            ->schema([
                                Section::make('Profile Picture')
                                    ->description('Upload or update your profile photo')
                                    ->schema([                                        Forms\Components\FileUpload::make('profile_photo')
                                            ->label('Profile Photo')
                                            ->image()
                                            ->directory('profile_photos')
                                            ->disk('public')
                                            ->visibility('public')
                                            ->maxSize(2048) // 2MB
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->imageEditor()
                                            ->imageEditorAspectRatios([
                                                '1:1',
                                            ])
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('1:1')
                                            ->imageResizeTargetWidth('300')
                                            ->imageResizeTargetHeight('300')
                                            ->helperText('Recommended: Square image (1:1 aspect ratio), max 2MB')
                                            ->columnSpanFull()
                                            ->extraAttributes([
                                                'onchange' => 'console.log("Photo upload started")'
                                            ]),
                                    ]),
                            ]),

                        // Account Information Tab
                        Tabs\Tab::make('Account Info')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Account Details')
                                    ->description('View your account information and statistics')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Forms\Components\Placeholder::make('role')
                                                ->label('Role')
                                                ->content(fn () => ucfirst(Auth::user()->role ?? 'User')),
                                            Forms\Components\Placeholder::make('status')
                                                ->label('Status')
                                                ->content(fn () => ucfirst(Auth::user()->status ?? 'Active')),
                                            Forms\Components\Placeholder::make('joined')
                                                ->label('Member Since')
                                                ->content(fn () => Auth::user()->created_at?->format('F Y') ?? 'Unknown'),
                                        ]),
                                        
                                        Grid::make(2)->schema([
                                            Forms\Components\Placeholder::make('email_verified')
                                                ->label('Email Verification')
                                                ->content(fn () => Auth::user()->email_verified_at ? '✅ Verified' : '❌ Not Verified'),
                                            Forms\Components\Placeholder::make('last_login')
                                                ->label('Last Activity')
                                                ->content(fn () => Auth::user()->updated_at?->diffForHumans() ?? 'Unknown'),
                                        ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ])
            ->statePath('data');
    }    protected function getHeaderActions(): array
    {
        return [
            Action::make('updateProfile')
                ->label('Save Profile')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->keyBindings(['mod+s'])
                ->action('save')
                ->requiresConfirmation()
                ->modalHeading('Update Profile')
                ->modalDescription('Are you sure you want to save these profile changes?')
                ->modalSubmitActionLabel('Yes, Update'),

            Action::make('downloadData')
                ->label('Download Data')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action('downloadProfileData')
                ->tooltip('Export your profile data'),

            Action::make('refreshProfile')
                ->label('Refresh')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action('refreshProfile')
                ->keyBindings(['f5']),
        ];
    }    public function save(): void
    {
        try {
            $data = $this->form->getState();
            
            $user = Auth::user();
            
            // Handle password change if provided
            if (!empty($data['current_password']) && !empty($data['new_password'])) {
                if (!Hash::check($data['current_password'], $user->password)) {
                    Notification::make()
                        ->title('Current Password Incorrect')
                        ->danger()
                        ->body('The current password you entered is incorrect.')
                        ->send();
                    return;
                }
                
                $data['password'] = Hash::make($data['new_password']);
                unset($data['current_password'], $data['new_password'], $data['new_password_confirmation']);
            } else {
                // Remove password fields if not changing password
                unset($data['current_password'], $data['new_password'], $data['new_password_confirmation']);
            }
            
            $user->update($data);
            
            // Refresh the form
            $this->mount();
            
            Notification::make()
                ->title('Profile Updated Successfully! 🎉')
                ->success()
                ->body('Your profile information has been updated successfully.')
                ->duration(5000)
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Update Failed')
                ->danger()
                ->body('Failed to update profile: ' . $e->getMessage())
                ->persistent()
                ->send();
        }
    }

    public function refreshProfile(): void
    {
        $this->mount();
        
        Notification::make()
            ->title('Profile Refreshed')
            ->info()
            ->body('Profile data has been refreshed from the database.')
            ->send();
    }

    public function getUserStatistics(): array
    {
        $user = Auth::user();
        
        return [
            'days_with_us' => $user->created_at ? $user->created_at->diffInDays(now()) : 0,
            'profile_completion' => $this->calculateProfileCompletion(),
            'last_activity' => $user->updated_at ? $user->updated_at->diffForHumans() : 'Unknown',
            'total_logins' => 'N/A', // This would require additional tracking
            'security_score' => $this->calculateSecurityScore(),
        ];
    }

    private function calculateProfileCompletion(): int
    {
        $user = Auth::user();
        $fields = ['name', 'lastname', 'email', 'phone', 'address', 'birth_date', 'profile_photo'];
        $completed = 0;
        
        foreach ($fields as $field) {
            if (!empty($user->$field)) {
                $completed++;
            }
        }
        
        // Add email verification bonus
        if ($user->email_verified_at) {
            $completed++;
        }
        
        return round(($completed / 8) * 100);
    }

    private function calculateSecurityScore(): int
    {
        $user = Auth::user();
        $score = 0;
        
        // Password (assumed secure if it exists)
        if ($user->password) $score += 30;
        
        // Email verification
        if ($user->email_verified_at) $score += 25;
        
        // Phone verification (if exists)
        if ($user->phone) $score += 20;
        
        // Profile photo (reduces impersonation risk)
        if ($user->profile_photo) $score += 15;
        
        // Recent activity (active account)
        if ($user->updated_at && $user->updated_at->diffInDays(now()) <= 7) $score += 10;
        
        return min($score, 100);
    }

    public function downloadProfileData(): void
    {
        try {
            $user = Auth::user();
            $data = [
                'profile_export' => [
                    'generated_at' => now()->toISOString(),
                    'user_id' => $user->id,
                    'personal_information' => [
                        'name' => $user->name,
                        'midname' => $user->midname,
                        'lastname' => $user->lastname,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'address' => $user->address,
                        'birth_date' => $user->birth_date,
                    ],
                    'account_information' => [
                        'role' => $user->role,
                        'status' => $user->status,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                        'email_verified_at' => $user->email_verified_at,
                    ],
                    'statistics' => $this->getUserStatistics(),
                ]
            ];
            
            $filename = 'profile-data-' . now()->format('Y-m-d-H-i-s') . '.json';
            
            Notification::make()
                ->title('Profile Data Export')
                ->success()
                ->body('Your profile data has been prepared for download.')
                ->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->button()
                        ->url('data:application/json;charset=utf-8,' . urlencode(json_encode($data, JSON_PRETTY_PRINT)))
                        ->openUrlInNewTab()
                ])
                ->persistent()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Export Failed')
                ->danger()
                ->body('Failed to export profile data: ' . $e->getMessage())
                ->send();
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check();
    }
}
