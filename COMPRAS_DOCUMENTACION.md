# Módulo de Compras - Documentación

## Descripción General

El módulo de compras permite registrar y gestionar las compras que realiza la papelería a sus proveedores, incluyendo:

- Crear nuevas compras
- Editar compras pendientes
- Marcar compras como recibidas (actualizando automáticamente el stock)
- Cancelar compras
- Generar facturas de compra
- Validación de que todos los productos pertenecen al mismo proveedor

## Estructura Base de Datos

### Tabla: `compras`
Registra las compras realizadas a proveedores.

**Campos principales:**
- `id` - ID único de la compra
- `numero_compra` - Código único generado automáticamente (COM-000001)
- `fecha_compra` - Fecha y hora de la compra
- `proveedor_ruc` - RUC del proveedor (clave foránea)
- `subtotal` - Subtotal de la compra
- `iva` - Monto del IVA (12%)
- `total` - Total a pagar
- `descripcion` - Notas sobre la compra (opcional)
- `estado` - Estado de la compra (pendiente, recibida, cancelada, anulada)
- `usuario_id` - Usuario que registró la compra (clave foránea)
- `tipo_pago_id` - Tipo de pago (clave foránea)
- `fecha_recepcion` - Fecha cuando se recibe la compra
- `observaciones` - Observaciones adicionales

### Tabla: `compra_detalles`
Registra los productos incluidos en cada compra.

**Campos principales:**
- `id` - ID único del detalle
- `compra_id` - ID de la compra (clave foránea)
- `producto_id` - ID del producto (clave foránea)
- `cantidad` - Cantidad comprada
- `precio_unitario` - Precio unitario del producto
- `subtotal` - Subtotal del detalle (cantidad × precio_unitario)

## Modelos Eloquent

### Modelo: `Compra`

**Relaciones:**
```php
$compra->proveedor()    // Proveedor de la compra
$compra->usuario()      // Usuario que registró
$compra->tipoPago()     // Tipo de pago
$compra->detalles()     // Detalles/items de la compra
```

**Métodos útiles:**
```php
Compra::generarNumeroCompra()  // Genera número único (COM-000001)
$compra->calcularTotal()       // Calcula subtotal, IVA y total
```

### Modelo: `CompraDetalle`

**Relaciones:**
```php
$detalle->compra()              // Compra a la que pertenece
$detalle->producto()            // Producto del detalle
```

**Métodos útiles:**
```php
$detalle->calcularSubtotal()    // Calcula subtotal (cantidad × precio)
```

## Controlador: `CompraController`

### Métodos disponibles:

#### `index()` - Listar compras
- Muestra un listado paginado de todas las compras
- Incluye información del proveedor, usuario y totales
- Permite filtrar y buscar compras

#### `create()` - Formulario nueva compra
- Muestra el formulario para crear una nueva compra
- Carga los proveedores y tipos de pago disponibles

#### `store()` - Guardar nueva compra
- Valida los datos ingresados
- Verifica que todos los productos pertenecen al mismo proveedor
- Crea la compra y sus detalles
- Calcula automáticamente los totales

#### `show()` - Ver detalle de compra
- Muestra toda la información de una compra
- Listado de productos incluidos
- Resumen de totales
- Acciones disponibles según el estado

#### `edit()` - Editar compra
- Permite editar solo compras en estado "pendiente"
- Carga la información actual de la compra

#### `update()` - Actualizar compra
- Valida los datos
- Verifica productos del proveedor
- Actualiza la compra y sus detalles

#### `recibir()` - Marcar como recibida
- Cambia el estado de "pendiente" a "recibida"
- **IMPORTANTE:** Actualiza automáticamente el stock de todos los productos
- Registra la fecha de recepción

#### `cancelar()` - Cancelar compra
- Cambia el estado a "anulada"
- Solo disponible para compras pendientes
- Requiere especificar la razón de cancelación

#### `obtenerProductosProveedor()` - API AJAX
- Retorna los productos disponibles de un proveedor en formato JSON
- Incluye código, nombre, precio de costo e información de IVA
- Utilizada en los formularios de crear/editar para cargar dinámicamente los productos

#### `generarFactura()` - Generar factura
- Prepara los datos para generar una factura en PDF
- Retorna información en formato JSON

## Validación de Productos por Proveedor

### Implementación:

La validación se realiza en el método `validarProductosDelProveedor()`:

```php
private function validarProductosDelProveedor($proveedorRuc, $detalles)
```

**Proceso:**
1. Itera sobre cada producto en los detalles de la compra
2. Verifica que exista una relación en la tabla `productos_proveedores`
3. Si algún producto no pertenece al proveedor, lanza una excepción

**Dónde se ejecuta:**
- Al crear una compra (`store()`)
- Al actualizar una compra (`update()`)

## Vistas Blade

### `index.blade.php` - Listado de compras
- Tabla con todas las compras
- Columnas: Nº, Fecha, Proveedor, Subtotal, IVA, Total, Estado, Usuario
- Acciones: Ver, Editar, Marcar como Recibida
- Paginación incluida

### `create.blade.php` - Crear compra
- Formulario con dos secciones:
  - **Información General:** Proveedor, Fecha, Tipo de Pago, Descripción
  - **Resumen:** Muestra en tiempo real subtotal, IVA y total
- Tabla dinámica para agregar productos
- Validaciones en cliente y servidor

### `edit.blade.php` - Editar compra
- Similar a create.blade.php pero con datos pre-cargados
- Solo editable si el estado es "pendiente"

### `show.blade.php` - Detalle de compra
- Información completa de la compra
- Tabla de productos con detalles
- Resumen de totales
- Acciones contextuales según el estado

## Rutas Disponibles

```
GET    /admin/compras                              // Listar compras
GET    /admin/compras/crear                        // Formulario crear
POST   /admin/compras                              // Guardar nueva
GET    /admin/compras/{compra}                     // Ver detalle
GET    /admin/compras/{compra}/editar              // Formulario editar
PUT    /admin/compras/{compra}                     // Actualizar
POST   /admin/compras/{compra}/recibir             // Marcar recibida
POST   /admin/compras/{compra}/cancelar            // Cancelar compra
GET    /admin/compras/productos-proveedor/{ruc}    // Obtener productos (AJAX)
GET    /admin/compras/{compra}/factura             // Generar factura
```

## Características Principales

### 1. Generación Automática de Número de Compra
- Formato: `COM-XXXXXX` (6 dígitos)
- Único para cada compra
- Generado automáticamente al crear

### 2. Cálculo Automático de Totales
- Subtotal: suma de todos los detalles
- IVA: calculado al 12% para productos que tengan IVA
- Total: subtotal + IVA
- Se actualiza automáticamente con JavaScript en tiempo real

### 3. Actualización de Stock
- Al recibir una compra, aumenta automáticamente el stock de cada producto
- Registra la cantidad exacta de la compra

### 4. Estados de Compra
- **Pendiente:** Recién creada, editable
- **Recibida:** Stock actualizado, sin cambios
- **Cancelada:** Pagada sin recibir mercadería
- **Anulada:** Cancelada, registra razón

### 5. Validación de Proveedor
- Asegura que solo se agreguen productos del proveedor seleccionado
- Se valida tanto en cliente como en servidor

## Instalación y Configuración

### 1. Ejecutar migraciones
```bash
php artisan migrate
```

### 2. Ejecutar seeders (opcional, para tipos de pago)
```bash
php artisan db:seed --class=TipoPagoSeeder
```

### 3. Verificar estructura de carpetas
```
app/Models/Compra.php
app/Models/CompraDetalle.php
app/Http/Controllers/CompraController.php
resources/views/admin/compras/index.blade.php
resources/views/admin/compras/create.blade.php
resources/views/admin/compras/edit.blade.php
resources/views/admin/compras/show.blade.php
```

## Funcionalidades Futuras

- [ ] Generación de PDF con factura de compra
- [ ] Descuentos en compras
- [ ] Devoluciones de compras
- [ ] Reportes de compras por proveedor
- [ ] Alertas de stock mínimo
- [ ] Integración con sistema de pagos
- [ ] Historial de cambios en compras
- [ ] Asignación de usuarios revisores

## Notas Importantes

1. **Stock:** El stock solo se actualiza cuando la compra es marcada como "recibida"
2. **Edición:** Solo se pueden editar compras en estado "pendiente"
3. **Cancelación:** La cancelación no afecta el stock
4. **Proveedor:** No se puede cambiar de proveedor si ya tiene detalles agregados
5. **IVA:** Se aplica según la configuración del producto (campo `tiene_iva`)

## Requisitos Previos

- Tabla `proveedores` debe existir
- Tabla `productos` debe existir
- Tabla `productos_proveedores` debe existir (relación muchos a muchos)
- Tabla `tipo_pagos` debe existir
- Tabla `users` debe existir

## Soporte

Para reportar errores o solicitar mejoras, contacte al equipo de desarrollo.
