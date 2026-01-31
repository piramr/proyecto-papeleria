<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Ajustes de Cuenta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" 
             x-data="{ activeTab: window.location.hash ? window.location.hash.substring(1) : 'profile' }"
             @hashchange.window="activeTab = window.location.hash.substring(1)">
            
            <div class="row">
                <!-- Content -->
                <div class="col-md-12">
                    <!-- Profile Information -->
                    <div x-show="activeTab === 'profile'" x-transition.opacity>
                        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
                            @livewire('profile.update-profile-information-form')
                        @endif
                    </div>

                    <!-- Update Password -->
                    <div x-show="activeTab === 'password'" x-transition.opacity style="display: none;">
                        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
                             @livewire('profile.update-password-form')

                             <x-section-border />
                             
                             @livewire('profile.update-security-questions')
                        @endif
                    </div>

                    <!-- 2FA -->
                    <div x-show="activeTab === '2fa'" x-transition.opacity style="display: none;">
                        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                             @livewire('profile.two-factor-authentication-form')
                        @endif
                    </div>

                    <!-- Browser Sessions -->
                    <div x-show="activeTab === 'sessions'" x-transition.opacity style="display: none;">
                         @livewire('profile.logout-other-browser-sessions-form')
                    </div>

                    <!-- Delete Account -->
                    <div x-show="activeTab === 'delete'" x-transition.opacity style="display: none;">
                        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
                             @livewire('profile.delete-user-form')
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
