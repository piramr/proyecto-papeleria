<div class="card card-primary card-outline shadow-sm">
    <div class="card-header">
        <h3 class="card-title">{{ __('Sesiones de Navegador') }}</h3>
    </div>

    <div class="card-body">
        <div class="text-muted mb-4">
            {{ __('Administra y cierra tus sesiones activas en otros navegadores y dispositivos.') }}
        </div>

        <div class="text-sm text-muted">
            {{ __('Si es necesario, puedes cerrar sesión en todas tus otras sesiones de navegador en todos tus dispositivos. Algunas de tus sesiones recientes se enumeran a continuación; sin embargo, esta lista puede no ser exhaustiva. Si crees que tu cuenta se ha visto comprometida, también debes actualizar tu contraseña.') }}
        </div>

        @if (count($this->sessions) > 0)
            <div class="mt-4">
                <!-- Other Browser Sessions -->
                @foreach ($this->sessions as $session)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="mr-3">
                            @if ($session->agent->isDesktop())
                                <i class="fas fa-desktop fa-2x text-muted"></i>
                            @else
                                <i class="fas fa-mobile-alt fa-2x text-muted"></i>
                            @endif
                        </div>

                        <div>
                            <div class="text-sm text-muted">
                                {{ $session->agent->platform() ? $session->agent->platform() : __('Desconocido') }} - {{ $session->agent->browser() ? $session->agent->browser() : __('Desconocido') }}
                            </div>

                            <div>
                                <div class="text-xs text-muted">
                                    {{ $session->ip_address }},

                                    @if ($session->is_current_device)
                                        <span class="text-success font-weight-bold">{{ __('Este dispositivo') }}</span>
                                    @else
                                        {{ __('Última actividad') }} {{ $session->last_active }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="d-flex align-items-center mt-4">
            <button type="button" class="btn btn-primary" wire:click="confirmLogout" wire:loading.attr="disabled">
                {{ __('Cerrar Sesión en Otros Navegadores') }}
            </button>

            <x-action-message class="ml-3 text-success" on="loggedOut">
                {{ __('Hecho.') }}
            </x-action-message>
        </div>

        <!-- Log Out Other Devices Confirmation Modal -->
        @if($confirmingLogout)
            <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-modal="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold">{{ __('Cerrar Sesión en Otros Navegadores') }}</h5>
                            <button type="button" class="close" wire:click="$toggle('confirmingLogout')" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted text-sm">
                                {{ __('Por favor ingrese su contraseña para confirmar que desea cerrar sesión en sus otras sesiones de navegador en todos sus dispositivos.') }}
                            </p>
                            <div class="form-group" x-data="{}" x-init="setTimeout(() => $refs.password.focus(), 250)">
                                <input type="password" class="form-control" placeholder="{{ __('Contraseña') }}"
                                       x-ref="password"
                                       wire:model="password"
                                       wire:keydown.enter="logoutOtherBrowserSessions" />
                                <x-input-error for="password" class="mt-2 text-danger" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="$toggle('confirmingLogout')" wire:loading.attr="disabled">
                                {{ __('Cancelar') }}
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled">
                                {{ __('Cerrar Sesión en Otros Navegadores') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
