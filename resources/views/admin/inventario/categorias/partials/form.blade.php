<form method="POST" action="{{ route('categorias.store') }}">
    @csrf

    <div class="row">
        <div class="col-md-12">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Nombre de la categoría <span class="text-danger">*</span></label>
                    <input type="text" name="nombre" maxlength="100"
                        class="form-control @error('nombre') is-invalid @enderror" 
                        value="{{ old('nombre') }}" placeholder="Ej. Electrónica">
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-6">
                    <label>Descripción</label>
                    <input type="text" name="descripcion" maxlength="255"
                        class="form-control @error('descripcion') is-invalid @enderror" 
                        value="{{ old('descripcion') }}" placeholder="Breve descripción de la categoría">
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-3">
        <button type="button" class="btn btn-secondary mr-2" data-toggle="collapse" data-target="#formProveedor">
            Cancelar
        </button>

        <button type="submit" class="btn btn-primary">
            Guardar categoría
        </button>
    </div>
</form>