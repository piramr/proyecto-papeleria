# INSTRUCCIONES DE INSTALACIÓN - MÓDULO DE COMPRAS

## Paso 1: Migración de Base de Datos

El archivo de migración ya está creado en:
```
database/migrations/2026_01_21_000000_create_compras_table.php
```

Ejecuta la migración:
```bash
php artisan migrate
```

Esto creará las tablas:
- `compras` - Información principal de las compras
- `compra_detalles` - Detalles de productos en cada compra

## Paso 2: Verificar Tabla de Tipos de Pago

Asegúrate de que la tabla `tipo_pagos` existe en tu base de datos. Si no existe, ejecuta:

```bash
php artisan db:seed --class=TipoPagoSeeder
```

Esto agregará los tipos de pago:
- Efectivo
- Transferencia Bancaria
- Cheque
- Crédito

## Paso 3: Verificar Relaciones de Proveedor-Producto

La validación de que los productos pertenecen al proveedor utiliza la tabla `productos_proveedores`.

Asegúrate de que:
1. La tabla `productos_proveedores` tenga datos con la relación entre proveedores y productos
2. Los campos sean: `proveedor_ruc` y `producto_id`
3. Tenga un campo `precio_costo` (precio de compra)

Ejemplo de estructura esperada:
```sql
CREATE TABLE productos_proveedores (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    proveedor_ruc VARCHAR(13),
    producto_id BIGINT UNSIGNED,
    precio_costo DECIMAL(10,2),
    ...
    FOREIGN KEY (proveedor_ruc) REFERENCES proveedores(ruc),
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);
```

## Paso 4: Actualizar Modelos (Si es necesario)

### Actualizar Modelo `Proveedor`
Asegúrate que el modelo `Proveedor` tenga esta relación (ya debería estar):
```php
public function productos() {
    return $this->belongsToMany(Producto::class, 'productos_proveedores', 'proveedor_ruc', 'producto_id');
}
```

### Actualizar Modelo `Producto`
Asegúrate que el modelo `Producto` tenga esta relación:
```php
public function proveedores() {
    return $this->belongsToMany(Proveedor::class, 'productos_proveedores', 'producto_id', 'proveedor_ruc');
}
```

## Paso 5: Limpiar Cache (Recomendado)

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## Paso 6: Probar el Módulo

1. Inicia el servidor Laravel:
```bash
php artisan serve
```

2. Navega a: `http://localhost:8000/admin/compras`

3. Verifica que:
   - Aparece el botón "Nueva Compra"
   - Puedes crear una nueva compra
   - Se cargan los proveedores correctamente
   - Al seleccionar un proveedor, se cargan sus productos

## Datos Necesarios Previos

Para que el módulo funcione correctamente, necesitas:

1. **Al menos un Proveedor registrado** en la tabla `proveedores`
2. **Al menos un Producto registrado** en la tabla `productos`
3. **Relación entre Proveedor y Producto** en la tabla `productos_proveedores`
4. **Tipos de Pago** en la tabla `tipo_pagos`

## Solución de Problemas

### Error: "No hay productos disponibles"
- Verifica que existe la relación en `productos_proveedores`
- Asegúrate que el `proveedor_ruc` coincida exactamente

### Error: "El producto no es suministrado por este proveedor"
- Confirma que la relación existe en `productos_proveedores`
- Verifica los valores de `proveedor_ruc` y `producto_id`

### No aparece el módulo en el menú
- Verifica que las rutas estén bien en `routes/web.php`
- Limpia el cache: `php artisan route:clear`
- Verifica que tienes permisos de acceso

## Archivos Creados

```
app/Models/
    ├── Compra.php
    └── CompraDetalle.php

app/Http/Controllers/
    └── CompraController.php

resources/views/admin/compras/
    ├── index.blade.php
    ├── create.blade.php
    ├── edit.blade.php
    └── show.blade.php

database/migrations/
    └── 2026_01_21_000000_create_compras_table.php

database/seeders/
    └── TipoPagoSeeder.php

routes/
    └── web.php (actualizado)

Documentación:
    ├── COMPRAS_DOCUMENTACION.md
    └── INSTALAR_COMPRAS.md (este archivo)
```

## Próximos Pasos Recomendados

1. **Generar Facturas en PDF**
   - Instala una librería como `barryvdh/laravel-dompdf`
   - Implementa en el método `generarFactura()`

2. **Reportes**
   - Crear reportes de compras por proveedor
   - Análisis de gastos

3. **Notificaciones**
   - Alertas cuando llega stock mínimo
   - Recordatorios de pagos pendientes

4. **Auditoría**
   - Registrar cambios en compras
   - Historial de modificaciones

## Comandos Útiles

```bash
# Ver todas las rutas
php artisan route:list | grep compras

# Crear un seeder para datos de prueba
php artisan make:seeder CompraSeeder

# Resetear base de datos (CUIDADO: borra todo)
php artisan migrate:reset

# Hacer fresh migration (borra y recrea)
php artisan migrate:fresh --seed
```

## Soporte

Si encuentras problemas:
1. Revisa los logs: `storage/logs/laravel.log`
2. Consulta la documentación: `COMPRAS_DOCUMENTACION.md`
3. Verifica que todos los datos previos existan en la BD
