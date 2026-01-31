<div class="card card-primary card-outline shadow-sm">
    <div class="card-header">
        <h3 class="card-title">{{ __('Autenticación de Dos Factores') }}</h3>
    </div>

    <div class="card-body">
        <div class="text-muted mb-4">
            {{ __('Añade seguridad adicional a tu cuenta usando la autenticación de dos factores.') }}
        </div>

        <h5 class="font-weight-bold mb-3">
            @if ($this->enabled)
                @if ($showingConfirmation)
                    {{ __('Termina de habilitar la autenticación de dos factores.') }}
                @else
                    {{ __('Has habilitado la autenticación de dos factores.') }}
                @endif
            @else
                {{ __('No has habilitado la autenticación de dos factores.') }}
            @endif
        </h5>

        <p class="text-sm text-muted">
            {{ __('Cuando la autenticación de dos factores está habilitada, se te solicitará un token seguro y aleatorio durante la autenticación. Puedes obtener este token de la aplicación Google Authenticator de tu teléfono.') }}
        </p>

        @if ($this->enabled)
            @if ($showingQrCode)
                <div class="mt-4 text-muted">
                    <p class="font-weight-bold">
                        @if ($showingConfirmation)
                            {{ __('Para terminar de habilitar la autenticación de dos factores, escanea el siguiente código QR usando la aplicación de autenticación de tu teléfono o ingresa la clave de configuración y proporciona el código OTP generado.') }}
                        @else
                            {{ __('La autenticación de dos factores ahora está habilitada. Escanea el siguiente código QR usando la aplicación de autenticación de tu teléfono o ingresa la clave de configuración.') }}
                        @endif
                    </p>
                </div>

                <div class="mt-4 p-2 d-inline-block bg-white border rounded">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>

                <div class="mt-4 text-muted">
                    <p class="font-weight-bold">
                        {{ __('Clave de Configuración') }}: {{ decrypt($this->user->two_factor_secret) }}
                    </p>
                </div>

                @if ($showingConfirmation)
                    <div class="mt-4 form-group">
                        <label for="code">{{ __('Código') }}</label>
                        <input id="code" type="text" name="code" class="form-control w-50" inputmode="numeric" autofocus autocomplete="one-time-code"
                            wire:model="code"
                            wire:keydown.enter="confirmTwoFactorAuthentication" />
                        <x-input-error for="code" class="mt-2 text-danger" />
                    </div>
                @endif
            @endif

            @if ($showingRecoveryCodes)
                <div class="mt-4 text-muted">
                    <p class="font-weight-bold">
                        {{ __('Guarda estos códigos de recuperación en un administrador de contraseñas seguro. Se pueden usar para recuperar el acceso a tu cuenta si pierdes tu dispositivo de autenticación de dos factores.') }}
                    </p>
                </div>

                <div class="bg-light p-3 rounded mt-4">
                    <div class="row">
                        @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                            <div class="col-6 font-monospace mb-1">{{ $code }}</div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        <div class="mt-5">
            @if (! $this->enabled)
                <button type="button" class="btn btn-primary" wire:click="enableTwoFactorAuthentication" wire:loading.attr="disabled">
                    {{ __('Habilitar') }}
                </button>
            @else
                @if ($showingRecoveryCodes)
                    <button type="button" class="btn btn-secondary mr-3" wire:click="regenerateRecoveryCodes">
                        {{ __('Regenerar Códigos de Recuperación') }}
                    </button>
                @elseif ($showingConfirmation)
                    <button type="button" class="btn btn-primary mr-3" wire:click="confirmTwoFactorAuthentication" wire:loading.attr="disabled">
                        {{ __('Confirmar') }}
                    </button>
                @else
                    <button type="button" class="btn btn-secondary mr-3" wire:click="showRecoveryCodes">
                        {{ __('Mostrar Códigos de Recuperación') }}
                    </button>
                @endif

                @if ($showingConfirmation)
                    <button type="button" class="btn btn-secondary" wire:click="disableTwoFactorAuthentication" wire:loading.attr="disabled">
                        {{ __('Cancelar') }}
                    </button>
                @else
                    <button type="button" class="btn btn-danger" wire:click="disableTwoFactorAuthentication" wire:loading.attr="disabled">
                        {{ __('Deshabilitar') }}
                    </button>
                @endif
            @endif
        </div>

        <!-- Password Confirmation Modal -->
        @if($confirmingPassword)
            <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-modal="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left"> <!-- text-left to reset any parent alignment -->
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold">{{ __('Confirmar Contraseña') }}</h5>
                            <button type="button" class="close" wire:click="stopConfirmingPassword" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted text-sm">
                                {{ __('Por su seguridad, por favor confirme su contraseña para continuar.') }}
                            </p>
                            <div class="form-group">
                                <input type="password" class="form-control" placeholder="{{ __('Contraseña') }}"
                                       wire:model="confirmablePassword"
                                       wire:keydown.enter="confirmPassword" />
                                <x-input-error for="confirmablePassword" class="mt-2 text-danger" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="stopConfirmingPassword" wire:loading.attr="disabled">
                                {{ __('Cancelar') }}
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="confirmPassword" wire:loading.attr="disabled">
                                {{ __('Confirmar') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
