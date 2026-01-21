<form method="POST" action="{{ route('proveedores.store') }}" class="form-proveedor">
    @csrf

    <div class="row">

        <!-- ================= DATOS DEL PROVEEDOR ================= -->
        <div class="col-md-6">

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>RUC <span class="text-danger">*</span></label>
                    <input type="text" name="ruc" maxlength="13"
                        class="form-control @error('ruc') is-invalid @enderror" value="{{ old('ruc') }}">
                    @error('ruc')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label>Razón Social <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                        value="{{ old('nombre') }}">
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Teléfono principal <span class="text-danger">*</span></label>
                    <input type="text" name="telefono_principal"
                        class="form-control @error('telefono_principal') is-invalid @enderror"
                        value="{{ old('telefono_principal') }}">
                    @error('telefono_principal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label>Teléfono secundario</label>
                    <input type="text" name="telefono_secundario"
                        class="form-control @error('telefono_secundario') is-invalid @enderror"
                        value="{{ old('telefono_secundario') }}">
                    @error('telefono_secundario')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <!-- ================= DIRECCIONES ================= -->
        <div class="col-md-6">

            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Direcciones</strong>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddDireccion">
                    <i class="fas fa-plus"></i> Añadir
                </button>
            </div>

            {{-- ERROR GENERAL --}}
            @if ($errors->has('direcciones'))
                <div class="alert alert-danger">
                    {{ $errors->first('direcciones') }}
                </div>
            @endif

            <div id="direcciones-container">

                @foreach (old('direcciones', [0 => []]) as $i => $dir)
                    <div class="direccion-item border rounded p-2 mb-2 position-relative">

                        @if ($i > 0)
                            <button type="button" class="btn btn-sm btn-danger position-absolute"
                                style="top:5px; right:5px" onclick="this.parentElement.remove()">
                                <i class="fas fa-times"></i> Quitar
                            </button>
                        @endif

                        <small class="text-muted">
                            {{ $i == 0 ? 'Dirección principal' : 'Dirección adicional' }}
                        </small>

                        <div class="form-row mt-2">
                            <div class="form-group col-md-6">
                                <input type="text" name="direcciones[{{ $i }}][provincia]"
                                    placeholder="Provincia"
                                    class="form-control @error("direcciones.$i.provincia") is-invalid @enderror"
                                    value="{{ old("direcciones.$i.provincia") }}">
                                @error("direcciones.$i.provincia")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <input type="text" name="direcciones[{{ $i }}][ciudad]"
                                    placeholder="Ciudad"
                                    class="form-control @error("direcciones.$i.ciudad") is-invalid @enderror"
                                    value="{{ old("direcciones.$i.ciudad") }}">
                                @error("direcciones.$i.ciudad")
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <input type="text" name="direcciones[{{ $i }}][calle]" placeholder="Calle"
                                class="form-control @error("direcciones.$i.calle") is-invalid @enderror"
                                value="{{ old("direcciones.$i.calle") }}">
                            @error("direcciones.$i.calle")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <input type="text" name="direcciones[{{ $i }}][referencia]"
                                placeholder="Referencia"
                                class="form-control @error("direcciones.$i.referencia") is-invalid @enderror"
                                value="{{ old("direcciones.$i.referencia") }}">
                            @error("direcciones.$i.referencia")
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                @endforeach


            </div>
        </div>
    </div>

    <!-- ================= ACCIONES ================= -->
    <div class="d-flex justify-content-end mt-3">
        <button type="button" class="btn btn-secondary mr-2" data-toggle="collapse" data-target="#formProveedor">
            Cancelar
        </button>

        <button type="submit" class="btn btn-primary">
            Guardar proveedor
        </button>
    </div>

</form>
