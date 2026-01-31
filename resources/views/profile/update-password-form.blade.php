<div class="card card-primary card-outline shadow-sm">
    <div class="card-header">
        <h3 class="card-title">{{ __('Actualizar Contraseña') }}</h3>
    </div>

    <div class="card-body">
        <div class="text-muted mb-3">
            {{ __('Asegúrate de que tu cuenta esté usando una contraseña larga y aleatoria para mantenerse segura.') }}
        </div>

        <form wire:submit.prevent="updatePassword">
            <div class="form-group">
                <label for="current_password">{{ __('Contraseña Actual') }}</label>
                <input id="current_password" type="password" class="form-control" wire:model="state.current_password" autocomplete="current-password" />
                <x-input-error for="current_password" class="mt-2 text-danger" />
            </div>

            <div class="form-group">
                <label for="password">{{ __('Nueva Contraseña') }}</label>
                <input id="password" type="password" class="form-control" wire:model="state.password" autocomplete="new-password" />
                <x-input-error for="password" class="mt-2 text-danger" />
            </div>

            <div class="form-group">
                <label for="password_confirmation">{{ __('Confirmar Contraseña') }}</label>
                <input id="password_confirmation" type="password" class="form-control" wire:model="state.password_confirmation" autocomplete="new-password" />
                <x-input-error for="password_confirmation" class="mt-2 text-danger" />
            </div>

            <div class="d-flex justify-content-end">
                <x-action-message class="mr-3 text-success" on="saved">
                    {{ __('Guardado.') }}
                </x-action-message>

                <button type="submit" class="btn btn-primary">
                    {{ __('Guardar') }}
                </button>
            </div>
        </form>
    </div>
</div>
