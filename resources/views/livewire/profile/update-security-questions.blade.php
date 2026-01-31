<x-form-section submit="updateSecurityQuestion">
    <x-slot name="title">
        {{ __('Pregunta de Seguridad') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Establece una pregunta de seguridad para recuperar tu cuenta en caso de olvidar la contraseña.') }}
    </x-slot>

    <x-slot name="form">
        <!-- Question -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="question_id" value="{{ __('Pregunta') }}" />
            <select id="question_id" wire:model="question_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                <option value="">{{ __('Selecciona una pregunta') }}</option>
                @foreach($questions as $question)
                    <option value="{{ $question->id }}">{{ $question->question }}</option>
                @endforeach
            </select>
            <x-input-error for="question_id" class="mt-2" />
        </div>

        <!-- Answer -->
        <div class="col-span-6 sm:col-span-4">
            <x-label for="answer" value="{{ __('Respuesta') }}" />
            <x-input id="answer" type="text" class="mt-1 block w-full" wire:model="answer" autocomplete="off" />
            <x-input-error for="answer" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Guardado.') }}
        </x-action-message>

        <x-button>
            {{ __('Guardar') }}
        </x-button>
    </x-slot>
</x-form-section>
