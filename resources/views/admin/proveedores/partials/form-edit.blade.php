<form method="POST" action="{{ route('admin.proveedores.update', $proveedor->ruc) }}" class="form-proveedor">
    @csrf
    @method('PUT')

    <div class="row">

        <!-- ================= DATOS DEL PROVEEDOR ================= -->
        <div class="col-md-12">

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>RUC <span class="text-danger">*</span></label>
                    <input type="text" name="ruc" maxlength="13"
                        class="form-control @error('ruc') is-invalid @enderror" value="{{ old('ruc', $proveedor->ruc) }}">
                    @error('ruc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label>Razón Social <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                        value="{{ old('nombre', $proveedor->nombre) }}">
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group col-md-4">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $proveedor->email) }}">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Teléfono principal <span class="text-danger">*</span></label>
                    <input type="text" name="telefono_principal"
                        class="form-control @error('telefono_principal') is-invalid @enderror"
                        value="{{ old('telefono_principal', $proveedor->telefono_principal) }}">
                    @error('telefono_principal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-3">
                    <label>Teléfono secundario</label>
                    <input type="text" name="telefono_secundario"
                        class="form-control @error('telefono_secundario') is-invalid @enderror"
                        value="{{ old('telefono_secundario', $proveedor->telefono_secundario) }}">
                    @error('telefono_secundario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>


        </div>

        <!-- ================= DIRECCIONES ================= -->
        <div class="col-md-12">
            <div class="d-flex justify-content-start align-items-center mb-2">
                <strong>Direcciones</strong>
                <button type="button" class="btn btn-sm btn-outline-primary ml-2" id="btnAddDireccion">
                    <i class="fas fa-plus"></i> Añadir
                </button>
            </div>
        </div>

        <div id="direcciones-container" class="col-md-12">

            @foreach ($proveedor->direcciones as $i => $dir)
                <div class="direccion-item border rounded p-2 mb-2 position-relative">

                    @if ($i > 0)
                        <button type="button" class="btn btn-sm btn-danger position-absolute"
                            style="top:5px; right:5px" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif

                    <p class="text-muted d-block mb-1">
                        {{ $i == 0 ? 'Dirección principal' : 'Dirección adicional' }}
                    </p>
                    <!-- Input oculto para el id de la dirección -->
                    <input type="hidden" name="direcciones[{{ $i }}][id]" value="{{ $dir->id }}">

                    <div class="form-row align-items-start">

                        <div class="form-group col-md-3">
                            <input type="text" name="direcciones[{{ $i }}][provincia]"
                                placeholder="Provincia"
                                class="form-control @error("direcciones.$i.provincia") is-invalid @enderror"
                                value="{{ old("direcciones.$i.provincia", $dir->provincia) }}">
                            @error("direcciones.$i.provincia")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-3">
                            <input type="text" name="direcciones[{{ $i }}][ciudad]" placeholder="Ciudad"
                                class="form-control @error("direcciones.$i.ciudad") is-invalid @enderror"
                                value="{{ old("direcciones.$i.ciudad", $dir->ciudad) }}">
                            @error("direcciones.$i.ciudad")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-3">
                            <input type="text" name="direcciones[{{ $i }}][calle]" placeholder="Calle"
                                class="form-control @error("direcciones.$i.calle") is-invalid @enderror"
                                value="{{ old("direcciones.$i.calle", $dir->calle) }}">
                            @error("direcciones.$i.calle")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group col-md-3">
                            <input type="text" name="direcciones[{{ $i }}][referencia]"
                                placeholder="Referencia"
                                class="form-control @error("direcciones.$i.referencia") is-invalid @enderror"
                                value="{{ old("direcciones.$i.referencia", $dir->referencia) }}">
                            @error("direcciones.$i.referencia")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>
            @endforeach

        </div>

    </div>

    <div class="d-flex justify-content-end mt-3">
        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
            Cancelar
        </button>

        <button type="submit" class="btn btn-primary">
            Guardar cambios
        </button>
    </div>

</form>
