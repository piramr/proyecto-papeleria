<form method="POST" action="{{ isset($categoria) ? route('admin.categorias.update', $categoria) : route('admin.categorias.store') }}">
    @csrf
    @if(isset($categoria))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Nombre de la categoría <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" maxlength="100"
                        class="form-control @error('nombre') is-invalid @enderror"
                        value="{{ old('nombre', isset($categoria) ? $categoria->nombre : '') }}" placeholder="Ej. Electrónica">
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label>Descripción</label>
                    <input type="text" name="descripcion" maxlength="255"
                        class="form-control @error('descripcion') is-invalid @enderror"
                        value="{{ old('descripcion', isset($categoria) ? $categoria->descripcion : '') }}" placeholder="Breve descripción de la categoría">
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-3">
        @if(isset($isModal) && $isModal)
            <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">
                Cancelar
            </button>
        @else
            <button type="button" class="btn btn-secondary mr-2" data-toggle="collapse" data-target="#formProveedor">
                Cancelar
            </button>
        @endif

        <button type="submit" class="btn btn-primary">
            {{ isset($categoria) ? 'Actualizar categoría' : 'Guardar categoría' }}
        </button>
    </div>
</form>
