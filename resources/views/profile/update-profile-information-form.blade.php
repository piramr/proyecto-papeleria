<div class="card card-primary card-outline shadow-sm" x-data="{ isEditing: false }">
    <div class="card-header">
        <h3 class="card-title">{{ __('Información del Perfil') }}</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    
    <div class="card-body">
        <!-- View Mode -->
        <div x-show="!isEditing">
            <div class="text-center mb-4">
                <img src="{{ $this->user->profile_photo_url }}" 
                     alt="{{ $this->user->name }}" 
                     class="profile-user-img img-fluid img-circle" 
                     style="width: 120px; height: 120px; object-fit: cover;">
                
                <h3 class="profile-username text-center mt-3 font-weight-bold">{{ $this->user->nombres }} {{ $this->user->apellidos }}</h3>
                <p class="text-muted text-center mb-1">
                    {{ $this->user->getRoleNames()->implode(', ') ?: __('Sin Rol') }}
                </p>
                <p class="text-muted text-center">{{ $this->user->email }}</p>
            </div>

            <hr>

            <div class="mb-4">
                <h5 class="mb-3"><i class="fas fa-id-card mr-2 text-primary"></i> {{ __('Información personal') }}</h5>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <strong>{{ __('Cédula:') }}</strong>
                        <span class="d-block text-muted">{{ $this->user->cedula }}</span>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>{{ __('Teléfono:') }}</strong>
                        <span class="d-block text-muted">{{ $this->user->telefono ?: __('No registrado') }}</span>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>{{ __('Género:') }}</strong>
                        <span class="d-block text-muted">{{ $this->user->genero ?: __('No registrado') }}</span>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>{{ __('Dirección:') }}</strong>
                        <span class="d-block text-muted">{{ $this->user->direccion ?: __('No registrada') }}</span>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="button" class="btn btn-primary px-4" @click="isEditing = true">
                    <i class="fas fa-edit mr-2"></i> {{ __('Modificar mis datos') }}
                </button>
            </div>
        </div>

        <!-- Edit Mode -->
        <div x-show="isEditing" style="display: none;">
            <form wire:submit.prevent="updateProfileInformation">
                <!-- Profile Photo -->
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div x-data="{photoName: null, photoPreview: null}" class="form-group text-center mb-4">
                        <!-- Profile Photo File Input -->
                        <input type="file" id="photo" class="d-none"
                                    wire:model.live="photo"
                                    x-ref="photo"
                                    x-on:change="
                                            photoName = $refs.photo.files[0].name;
                                            const reader = new FileReader();
                                            reader.onload = (e) => {
                                                photoPreview = e.target.result;
                                            };
                                            reader.readAsDataURL($refs.photo.files[0]);
                                    " />

                        <!-- Current Profile Photo -->
                        <div class="mt-2" x-show="! photoPreview">
                            <img src="{{ $this->user->profile_photo_url }}" alt="{{ $this->user->name }}" class="rounded-circle" style="height: 100px; width: 100px; object-fit: cover;">
                        </div>

                        <!-- New Profile Photo Preview -->
                        <div class="mt-2" x-show="photoPreview" style="display: none;">
                            <span class="d-block rounded-circle mx-auto"
                                  style="height: 100px; width: 100px; background-size: cover; background-position: center;"
                                  x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                            </span>
                        </div>

                        <button type="button" class="btn btn-secondary btn-sm mt-3" x-on:click.prevent="$refs.photo.click()">
                            {{ __('Seleccionar Nueva Foto') }}
                        </button>

                        @if ($this->user->profile_photo_path)
                            <button type="button" class="btn btn-danger btn-sm mt-3 ml-2" wire:click="deleteProfilePhoto">
                                {{ __('Eliminar Foto') }}
                            </button>
                        @endif

                        <x-input-error for="photo" class="mt-2 text-danger" />
                    </div>
                @endif

                <div class="row">
                    <!-- Nombres -->
                    <div class="col-md-6 form-group">
                        <label for="nombres">{{ __('Nombres') }}</label>
                        <input id="nombres" type="text" class="form-control" wire:model="state.nombres" required autocomplete="nombres" />
                        <x-input-error for="nombres" class="mt-2 text-danger" />
                    </div>

                    <!-- Apellidos -->
                    <div class="col-md-6 form-group">
                        <label for="apellidos">{{ __('Apellidos') }}</label>
                        <input id="apellidos" type="text" class="form-control" wire:model="state.apellidos" required autocomplete="apellidos" />
                        <x-input-error for="apellidos" class="mt-2 text-danger" />
                    </div>
                </div>

                <div class="row">
                    <!-- Cédula -->
                    <div class="col-md-6 form-group">
                        <label for="cedula">{{ __('Cédula') }}</label>
                        <input id="cedula" type="text" class="form-control" wire:model="state.cedula" required autocomplete="cedula" />
                        <x-input-error for="cedula" class="mt-2 text-danger" />
                    </div>

                    <!-- Email -->
                    <div class="col-md-6 form-group">
                        <label for="email">{{ __('Correo Electrónico') }}</label>
                        <input id="email" type="email" class="form-control" wire:model="state.email" required autocomplete="username" />
                        <x-input-error for="email" class="mt-2 text-danger" />

                        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                            <p class="text-sm mt-2">
                                {{ __('Tu dirección de correo no está verificada.') }}

                                <button type="button" class="btn btn-link p-0 align-baseline" wire:click.prevent="sendEmailVerification">
                                    {{ __('Haz clic aquí para reenviar el correo de verificación.') }}
                                </button>
                            </p>

                            @if ($this->verificationLinkSent)
                                <p class="mt-2 font-weight-bold text-success">
                                    {{ __('Se ha enviado un nuevo enlace de verificación a tu correo.') }}
                                </p>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="row">
                    <!-- Teléfono -->
                    <div class="col-md-6 form-group">
                        <label for="telefono">{{ __('Teléfono') }}</label>
                        <input id="telefono" type="text" class="form-control" wire:model="state.telefono" />
                        <x-input-error for="telefono" class="mt-2 text-danger" />
                    </div>

                    <!-- Género -->
                    <div class="col-md-6 form-group">
                        <label for="genero">{{ __('Género') }}</label>
                        <select id="genero" class="form-control" wire:model="state.genero">
                            <option value="">{{ __('Seleccione...') }}</option>
                            <option value="Masculino">{{ __('Masculino') }}</option>
                            <option value="Femenino">{{ __('Femenino') }}</option>
                            <option value="Otro">{{ __('Otro') }}</option>
                        </select>
                        <x-input-error for="genero" class="mt-2 text-danger" />
                    </div>
                </div>

                <!-- Dirección -->
                <div class="form-group">
                    <label for="direccion">{{ __('Dirección Residencial') }}</label>
                    <input id="direccion" type="text" class="form-control" wire:model="state.direccion" />
                    <x-input-error for="direccion" class="mt-2 text-danger" />
                </div>

                <div class="d-flex justify-content-end align-items-center mt-4">
                    <button type="button" class="btn btn-secondary mr-2" @click="isEditing = false">
                        {{ __('Cancelar') }}
                    </button>
                    
                    <x-action-message class="mr-3 text-success" on="saved">
                        {{ __('Guardado.') }}
                    </x-action-message>

                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="photo">
                        {{ __('Guardar Cambios') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
