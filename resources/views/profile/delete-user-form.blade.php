<div class="card card-danger card-outline shadow-sm">
    <div class="card-header">
        <h3 class="card-title">{{ __('Eliminar Cuenta') }}</h3>
    </div>

    <div class="card-body">
        <div class="text-muted mb-4">
            {{ __('Eliminar permanentemente tu cuenta.') }}
        </div>

        <div class="text-sm text-muted">
            {{ __('Una vez que se elimine tu cuenta, todos sus recursos y datos se eliminarán permanentemente. Antes de eliminar tu cuenta, descarga cualquier dato o información que desees conservar.') }}
        </div>

        <div class="mt-4">
            <button type="button" class="btn btn-danger" wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                {{ __('Eliminar Cuenta') }}
            </button>
        </div>

        <!-- Delete User Confirmation Modal -->
        @if($confirmingUserDeletion)
            <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-modal="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content text-left">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold">{{ __('Eliminar Cuenta') }}</h5>
                            <button type="button" class="close" wire:click="$toggle('confirmingUserDeletion')" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted text-sm">
                                {{ __('¿Estás seguro de que deseas eliminar tu cuenta? Una vez que se elimine tu cuenta, todos sus recursos y datos se eliminarán permanentemente. Por favor, introduce tu contraseña para confirmar que deseas eliminar permanentemente tu cuenta.') }}
                            </p>
                            <div class="form-group" x-data="{}" x-init="setTimeout(() => $refs.password.focus(), 250)">
                                <input type="password" class="form-control" placeholder="{{ __('Contraseña') }}"
                                       x-ref="password"
                                       wire:model="password"
                                       wire:keydown.enter="deleteUser" />
                                <x-input-error for="password" class="mt-2 text-danger" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                                {{ __('Cancelar') }}
                            </button>
                            <button type="button" class="btn btn-danger" wire:click="deleteUser" wire:loading.attr="disabled">
                                {{ __('Eliminar Cuenta') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
