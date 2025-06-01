<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Profile Header Card --}}
        <x-filament::card>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @if($this->profileData['profile_photo'])
                            <img class="w-24 h-24 rounded-full object-cover border-4 border-primary-500 shadow-lg" 
                                 src="{{ Storage::disk('public')->url($this->profileData['profile_photo']) }}" 
                                 alt="Profile Photo">
                        @else
                            <div class="w-24 h-24 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center border-4 border-primary-500 shadow-lg">
                                <x-heroicon-o-user class="w-14 h-14 text-white" />
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $this->profileData['name'] }} {{ $this->profileData['midname'] }} {{ $this->profileData['lastname'] }}
                        </h2>
                        <p class="text-lg text-gray-600 dark:text-gray-300 mt-1">{{ $this->profileData['email'] }}</p>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                {{ Auth::user()->role === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : 
                                   (Auth::user()->role === 'manager' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                   'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                                <x-heroicon-o-user-circle class="w-4 h-4 mr-1" />
                                {{ ucfirst(Auth::user()->role ?? 'User') }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                {{ Auth::user()->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                   'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                <div class="w-2 h-2 rounded-full mr-2 
                                    {{ Auth::user()->status === 'active' ? 'bg-green-400' : 'bg-red-400' }}"></div>
                                {{ ucfirst(Auth::user()->status ?? 'Active') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                {{-- Quick Stats --}}
                <div class="hidden lg:flex lg:space-x-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            {{ Auth::user()->created_at?->diffInDays(now()) ?? 0 }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Days with us</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ Auth::user()->email_verified_at ? '✓' : '✗' }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Email Status</div>
                    </div>
                </div>
            </div>
        </x-filament::card>

        {{-- Profile Management Forms --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Profile Update Form --}}
            <div class="lg:col-span-2">
                <x-filament::card>
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <x-heroicon-o-pencil-square class="w-5 h-5 mr-2 text-primary-500" />
                            Profile Information
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Update your personal information and preferences</p>
                    </div>
                    <form wire:submit.prevent="updateProfile">
                        {{ $this->profileForm }}
                    </form>
                </x-filament::card>

                {{-- Password Change Form --}}
                <x-filament::card class="mt-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <x-heroicon-o-key class="w-5 h-5 mr-2 text-warning-500" />
                            Security Settings
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Change your account password for better security</p>
                    </div>
                    <form wire:submit.prevent="updatePassword">
                        {{ $this->passwordForm }}
                    </form>
                </x-filament::card>
            </div>

            {{-- Account Information Sidebar --}}
            <div class="space-y-6">
                {{-- Account Summary --}}
                <x-filament::card>
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <x-heroicon-o-information-circle class="w-5 h-5 mr-2 text-info-500" />
                            Account Summary
                        </h3>
                    </div>
                    <dl class="space-y-4">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-medium">
                                {{ Auth::user()->created_at?->format('M j, Y') ?? 'Unknown' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">
                                {{ Auth::user()->updated_at?->diffForHumans() ?? 'Unknown' }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Verification</dt>
                            <dd class="text-sm">
                                @if(Auth::user()->email_verified_at)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <x-heroicon-o-check-circle class="w-3 h-3 mr-1" />
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        <x-heroicon-o-x-circle class="w-3 h-3 mr-1" />
                                        Not Verified
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone Status</dt>
                            <dd class="text-sm">
                                @if(Auth::user()->phone)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <x-heroicon-o-phone class="w-3 h-3 mr-1" />
                                        Added
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                        <x-heroicon-o-phone-x-mark class="w-3 h-3 mr-1" />
                                        Not Added
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </x-filament::card>

                {{-- Quick Actions --}}
                <x-filament::card>
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <x-heroicon-o-bolt class="w-5 h-5 mr-2 text-amber-500" />
                            Quick Actions
                        </h3>
                    </div>
                    <div class="space-y-3">
                        <button type="button" 
                                wire:click="refreshProfile" 
                                class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <x-heroicon-o-arrow-path class="w-4 h-4 mr-2" />
                            Refresh Data
                        </button>
                        <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                            Last refreshed: {{ now()->format('g:i A') }}
                        </div>
                    </div>
                </x-filament::card>                {{-- Profile Completion --}}
                <x-filament::card>
                    @php
                        $user = Auth::user();
                        $completedFields = 0;
                        $totalFields = 8;
                        
                        if ($user->name) $completedFields++;
                        if ($user->lastname) $completedFields++;
                        if ($user->email) $completedFields++;
                        if ($user->phone) $completedFields++;
                        if ($user->address) $completedFields++;
                        if ($user->birth_date) $completedFields++;
                        if ($user->profile_photo) $completedFields++;
                        if ($user->email_verified_at) $completedFields++;
                        
                        $completionPercentage = round(($completedFields / $totalFields) * 100);
                    @endphp
                    
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <x-heroicon-o-chart-pie class="w-5 h-5 mr-2 text-green-500" />
                            Profile Completion
                        </h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Progress</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $completionPercentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-500" 
                                 style="width: {{ $completionPercentage }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $completedFields }} of {{ $totalFields }} fields completed
                        </div>
                        
                        {{-- Completion Status Badge --}}
                        @if($completionPercentage >= 100)
                            <div class="mt-3 p-2 bg-green-50 border border-green-200 rounded-lg dark:bg-green-900/20 dark:border-green-800">
                                <div class="flex items-center">
                                    <x-heroicon-o-check-circle class="w-4 h-4 text-green-600 dark:text-green-400 mr-2" />
                                    <span class="text-sm font-medium text-green-800 dark:text-green-200">Profile Complete!</span>
                                </div>
                            </div>
                        @elseif($completionPercentage >= 80)
                            <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded-lg dark:bg-yellow-900/20 dark:border-yellow-800">
                                <div class="flex items-center">
                                    <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-yellow-600 dark:text-yellow-400 mr-2" />
                                    <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Almost there!</span>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 p-2 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-800">
                                <div class="flex items-center">
                                    <x-heroicon-o-information-circle class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-2" />
                                    <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Complete your profile</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </x-filament::card>

                {{-- Profile Suggestions --}}
                @if($completionPercentage < 100)
                <x-filament::card>
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <x-heroicon-o-light-bulb class="w-5 h-5 mr-2 text-amber-500" />
                            Profile Tips
                        </h3>
                    </div>
                    <div class="space-y-2">
                        @if(!$user->phone)
                        <div class="flex items-start space-x-2 text-sm">
                            <x-heroicon-o-device-phone-mobile class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" />
                            <span class="text-gray-600 dark:text-gray-400">Add your phone number for better security</span>
                        </div>
                        @endif
                        
                        @if(!$user->birth_date)
                        <div class="flex items-start space-x-2 text-sm">
                            <x-heroicon-o-cake class="w-4 h-4 text-pink-500 mt-0.5 flex-shrink-0" />
                            <span class="text-gray-600 dark:text-gray-400">Add your birth date to complete your profile</span>
                        </div>
                        @endif
                        
                        @if(!$user->address)
                        <div class="flex items-start space-x-2 text-sm">
                            <x-heroicon-o-map-pin class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0" />
                            <span class="text-gray-600 dark:text-gray-400">Add your address for location-based features</span>
                        </div>
                        @endif
                        
                        @if(!$user->profile_photo)
                        <div class="flex items-start space-x-2 text-sm">
                            <x-heroicon-o-camera class="w-4 h-4 text-purple-500 mt-0.5 flex-shrink-0" />
                            <span class="text-gray-600 dark:text-gray-400">Upload a profile photo to personalize your account</span>
                        </div>
                        @endif
                        
                        @if(!$user->email_verified_at)
                        <div class="flex items-start space-x-2 text-sm">
                            <x-heroicon-o-envelope-open class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                            <span class="text-gray-600 dark:text-gray-400">Verify your email address for enhanced security</span>
                        </div>
                        @endif
                    </div>
                </x-filament::card>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
