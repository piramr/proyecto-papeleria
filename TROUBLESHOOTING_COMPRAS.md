# 🔧 GUÍA DE TROUBLESHOOTING - MÓDULO DE COMPRAS

## 🚨 Problemas Comunes y Soluciones

---

## 1. Error: "Class 'App\Http\Controllers\CompraController' not found"

### Problema
Al acceder a `/admin/compras` aparece error 500 con mensaje de clase no encontrada.

### Soluciones
```bash
# Opción 1: Regenerar autoload
composer dump-autoload

# Opción 2: Limpiar cache
php artisan cache:clear
php artisan route:clear

# Opción 3: Reiniciar servidor
# Detén el servidor (Ctrl+C) e inicia nuevamente
php artisan serve
```

### Verificación
```bash
# Verificar que el archivo existe
ls -la app/Http/Controllers/CompraController.php

# Verificar namespace en el archivo
head -5 app/Http/Controllers/CompraController.php
# Debe mostrar: namespace App\Http\Controllers;
```

---

## 2. Error: "SQLSTATE[42S02]: Table 'x.compras' doesn't exist"

### Problema
Las tablas de compras no existen en la base de datos.

### Soluciones
```bash
# Ejecutar todas las migraciones
php artisan migrate

# O si hay error:
php artisan migrate:rollback
php artisan migrate
```

### Verificación
```bash
# Verificar que las tablas existen
mysql -u root -p nombre_basedatos -e "SHOW TABLES LIKE 'compra%';"

# Debería mostrar:
# compras
# compra_detalles
```

### Si sigue sin funcionar
```bash
# Resetear base de datos (⚠️ BORRA TODO)
php artisan migrate:reset
php artisan migrate
```

---

## 3. Error: "SQLSTATE[HY000]: General error: 1005"

### Problema
Error de claves foráneas al ejecutar migraciones.

### Soluciones
```bash
# 1. Verificar que las tablas referenciadas existen
mysql -u root -p nombre_basedatos -e "SHOW TABLES;"

# Deben existir:
# - proveedores
# - productos
# - tipo_pagos
# - users

# 2. Si falta alguna, ejecuta las migraciones en orden:
php artisan migrate

# 3. Si aún hay problema, deshabilita checks temporalmente:
php artisan tinker
# Luego ejecuta:
DB::statement('SET FOREIGN_KEY_CHECKS=0');
DB::statement('SET FOREIGN_KEY_CHECKS=1');
exit
```

---

## 4. Error: "No hay productos disponibles para este proveedor"

### Problema
Al crear una compra, no carga los productos del proveedor.

### Soluciones

**Causa 1: Tabla `productos_proveedores` vacía**
```sql
-- Verificar si hay relaciones
SELECT * FROM productos_proveedores LIMIT 10;

-- Si está vacía, insertar datos:
INSERT INTO productos_proveedores (proveedor_ruc, producto_id, precio_costo) 
VALUES ('1234567890123', 1, 10.50);
```

**Causa 2: RUC no coincide**
```sql
-- Verificar que los RUC son idénticos
SELECT DISTINCT proveedor_ruc FROM productos_proveedores;
SELECT ruc FROM proveedores;

-- Asegúrate que son exactos (sin espacios extras)
```

**Causa 3: Producto_id no existe**
```sql
-- Verificar que los productos existen
SELECT id FROM productos WHERE id IN (
    SELECT producto_id FROM productos_proveedores
);

-- Si aparecen NULL, elimina esos registros:
DELETE FROM productos_proveedores 
WHERE producto_id NOT IN (SELECT id FROM productos);
```

---

## 5. Productos no cargan dinámicamente (AJAX)

### Problema
Seleccionar un proveedor no carga los productos en tiempo real.

### Soluciones

**Paso 1: Abrir consola del navegador**
- Presiona F12
- Abre la pestaña "Console"
- Selecciona un proveedor
- Busca mensajes de error

**Paso 2: Verificar logs del servidor**
```bash
tail -f storage/logs/laravel.log

# Debería haber una línea con GET request a:
# GET /admin/compras/productos-proveedor/1234567890123
```

**Paso 3: Probar endpoint AJAX manualmente**
```bash
# En otra terminal, mientras el servidor está corriendo:
curl http://localhost:8000/admin/compras/productos-proveedor/1234567890123

# Debería retornar un JSON con los productos
```

**Paso 4: Verificar tabla productos_proveedores**
```sql
-- Verificar que existen productos para el proveedor
SELECT * FROM productos_proveedores 
WHERE proveedor_ruc = '1234567890123'
LIMIT 5;
```

---

## 6. Stock no se actualiza al recibir compra

### Problema
Marcar compra como "recibida" no actualiza el stock de productos.

### Soluciones

**Verificación 1: Campo cantidad_stock existe**
```sql
-- Ver estructura de tabla productos
DESCRIBE productos;

-- Debe haber un campo: cantidad_stock (INT)

-- Si no existe, agregarlo:
ALTER TABLE productos ADD COLUMN cantidad_stock INT DEFAULT 0;
```

**Verificación 2: Compra existe y es válida**
```sql
-- Verificar que la compra existe
SELECT * FROM compras WHERE id = 1;

-- Verificar que tiene detalles
SELECT * FROM compra_detalles WHERE compra_id = 1;
```

**Verificación 3: Revisar logs de error**
```bash
tail -100 storage/logs/laravel.log | grep -i error
tail -100 storage/logs/laravel.log | grep -i compra

# Busca líneas con "Exception" o "Error"
```

**Verificación 4: Actualización manual (para verificar)**
```sql
-- Ver stock antes
SELECT id, nombre, cantidad_stock 
FROM productos 
WHERE id = 1;

-- Simular lo que debería hacer la compra
UPDATE productos 
SET cantidad_stock = cantidad_stock + 5
WHERE id = 1;

-- Ver stock después
SELECT id, nombre, cantidad_stock 
FROM productos 
WHERE id = 1;
```

---

## 7. Validación: "El producto no es suministrado por este proveedor"

### Problema
Al crear una compra, rechazo al guardar diciendo que un producto no pertenece al proveedor.

### Soluciones

**Causa 1: Relación no existe en BD**
```sql
-- Verificar que la relación existe:
SELECT * FROM productos_proveedores 
WHERE proveedor_ruc = '1234567890123' 
AND producto_id = 1;

-- Si no existe, crear:
INSERT INTO productos_proveedores (proveedor_ruc, producto_id, precio_costo)
VALUES ('1234567890123', 1, 15.50);
```

**Causa 2: ID de producto incorrecto**
- Verifica que estés usando el ID correcto del producto
- En el formulario, el atributo `value` debe ser el ID

```html
<!-- Verificar que el select tiene el value correcto -->
<option value="1">Producto 1</option>
<!-- value="1" es el ID del producto -->
```

**Causa 3: Cambiar proveedor después de agregar productos**
- Si cambias de proveedor después de agregar productos, el sistema rechazará
- Límpia los productos y vuelve a agregar

---

## 8. Totales calculados incorrectamente

### Problema
El subtotal, IVA o total no se calcula correctamente.

### Soluciones

**Verificación 1: Campo tiene_iva en productos**
```sql
-- Verificar que el campo existe
DESCRIBE productos;

-- Debe haber: tiene_iva (TINYINT o BOOLEAN)

-- Si no existe:
ALTER TABLE productos ADD COLUMN tiene_iva TINYINT DEFAULT 0;

-- Actualizar productos que tienen IVA
UPDATE productos SET tiene_iva = 1 WHERE id IN (1, 2, 3);
```

**Verificación 2: Cálculo manual**
```
Producto 1: 5 × $10 = $50 (SIN IVA)
Producto 2: 2 × $20 = $40 (CON IVA)

Subtotal: $50 + $40 = $90
IVA: $40 × 0.12 = $4.80 (solo Producto 2)
Total: $90 + $4.80 = $94.80
```

**Verificación 3: En JavaScript (abrir consola F12)**
```javascript
// Si ve los valores cálculados:
document.getElementById('resumenSubtotal').value
document.getElementById('resumenIva').value
document.getElementById('resumenTotal').value
```

**Verificación 4: En base de datos**
```sql
-- Ver totales guardados
SELECT id, numero_compra, subtotal, iva, total 
FROM compras 
WHERE id = 1;

-- Verificar detalles
SELECT producto_id, cantidad, precio_unitario, subtotal
FROM compra_detalles
WHERE compra_id = 1;
```

---

## 9. Paginación no funciona

### Problema
El listado de compras no muestra botones de paginación o redirige a página en blanco.

### Soluciones

**Causa 1: Blade syntax incorrecto**
```php
// Revisar que el código es:
{{ $compras->links() }}

// NO:
{{ $compras->pagination() }}
{{ $compras->paginate() }}
```

**Causa 2: Número de registros < 15**
- La paginación solo aparece si hay MÁS de 15 registros
- Crear datos de prueba si es necesario:
```bash
php artisan db:seed --class=CompraSeeder
```

**Causa 3: Bootstrap CSS no cargado**
- Verifica que Bootstrap 4 está en `resources/views/layouts/app.blade.php`

---

## 10. Modal de confirmación no funciona

### Problema
Los modales de "Marcar como recibida" o "Cancelar" no aparecen.

### Soluciones

**Verificación 1: jQuery cargado**
```html
<!-- En resources/views/layouts/app.blade.php -->
<!-- Debe tener jQuery antes de Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="...bootstrap.bundle.min.js"></script>
```

**Verificación 2: Bootstrap Modal script**
```javascript
// En show.blade.php, debe haber:
$('#modalRecibir').modal('show');
```

**Verificación 3: IDs del modal correctos**
```html
<!-- El ID debe coincidir en todos lados -->
<div class="modal fade" id="modalRecibir">...</div>

<!-- Y en JavaScript:-->
$('#modalRecibir').modal('show');
```

**Verificación 4: Consola del navegador (F12)**
- Si hay error como "$ is not defined"
- jQuery no está cargado o está en orden incorrecto

---

## 11. Error 403: Unauthorized

### Problema
Aparece error 403 al intentar crear o editar una compra.

### Soluciones

**Causa 1: No autenticado**
```
- Verifica que estés logueado
- Accede a http://localhost:8000/login si es necesario
```

**Causa 2: Middleware auth no funciona**
```php
// En routes/web.php debe estar en grupo:
Route::middleware(['auth:sanctum', ...])->group(function () {
    Route::prefix('admin/compras')->group(function () {
        // Rutas aquí
    });
});
```

**Causa 3: CSRF token faltante**
```html
<!-- Todos los formularios deben tener: -->
@csrf

<!-- En editar/crear también: -->
@method('PUT')  <!-- si es PUT -->
```

---

## 12. Archivo no se actualiza después de cambios

### Problema
Hice cambios en un archivo PHP pero los cambios no aparecen.

### Soluciones
```bash
# Limpiar todos los cachés
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Regenerar autoload
composer dump-autoload

# Reiniciar el servidor
# (Ctrl+C para detener)
php artisan serve
```

---

## 13. Base de datos vacía / Datos desaparecieron

### Problema
Las compras o productos desaparecieron de la base de datos.

### Soluciones

**NO HAGAS ESTO (borra todo):**
```bash
# ❌ NUNCA ejecutes esto en producción:
php artisan migrate:reset
php artisan migrate:refresh
```

**Si necesitas recuperar:**
1. **Revertir cambios en Git:**
   ```bash
   git status
   git checkout -- archivo.php
   ```

2. **Restaurar backup:**
   ```bash
   # Si tienes backup de la BD
   mysql -u root -p nombre_basedatos < backup.sql
   ```

3. **Crear datos nuevos:**
   ```bash
   php artisan db:seed --class=CompraSeeder
   ```

---

## 14. Email no se envía / Notificaciones no funcionan

### Problema
No se envían emails de confirmación de compras.

### Soluciones
```bash
# Este módulo NO envía emails por defecto
# Para agregar, necesitarías:

# 1. Crear evento/listener
php artisan make:event CompraCreada
php artisan make:listener EnviarCorreoCompra

# 2. Registrar en EventServiceProvider
# 3. Configurar MAIL en .env
```

**Nota:** Las notificaciones pueden agregarse como mejora futura.

---

## 15. El servidor se ralentiza / Laravel lento

### Problema
El aplicativo está muy lento.

### Soluciones

**Optimización 1: Verificar queries**
```php
// En AppServiceProvider.php agregar:
\DB::listen(function ($query) {
    \Log::info($query->sql);
});
```

**Optimización 2: Usar eager loading**
```php
// En CompraController index():
$compras = Compra::with(['proveedor', 'usuario'])
    ->latest()
    ->paginate(15);

// NO hacer esto:
$compras = Compra::all(); // Carga todas las compras
```

**Optimización 3: Agregar índices**
```sql
-- Si no existen (verificar en CHECKLIST_COMPRAS.md)
ALTER TABLE compras ADD INDEX (estado);
ALTER TABLE compras ADD INDEX (proveedor_ruc);
```

**Optimización 4: Limpiar logs grandes**
```bash
# Los logs pueden crecer demasiado
rm storage/logs/laravel.log
# Se recreará automáticamente
```

---

## 📋 Checklist de Diagnóstico Rápido

Cuando algo no funciona, sigue este orden:

```
1. [ ] ¿Migraciones ejecutadas?
   php artisan migrate

2. [ ] ¿Cache limpiado?
   php artisan cache:clear

3. [ ] ¿Autoload regenerado?
   composer dump-autoload

4. [ ] ¿Servidor reiniciado?
   Ctrl+C y php artisan serve

5. [ ] ¿Base de datos tiene datos?
   SELECT COUNT(*) FROM compras;

6. [ ] ¿Consola del navegador (F12) muestra errores?
   Revisar tab Console

7. [ ] ¿Logs del servidor muestran errores?
   tail -100 storage/logs/laravel.log

8. [ ] ¿Tabla estructura correcta?
   DESCRIBE compras;
```

---

## 🚀 Si nada funciona

```bash
# 1. Reset completo
php artisan migrate:reset
php artisan migrate
php artisan db:seed --class=TipoPagoSeeder

# 2. Limpiar todo
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload

# 3. Reiniciar servidor
php artisan serve

# 4. Verificar que todo funciona
mysql -e "SELECT COUNT(*) FROM compras;"
```

---

## 📞 Información para Reportar Bugs

Cuando reportes un problema, incluye:

```
1. Mensaje exacto del error
2. Stack trace (si aparece)
3. Navegador y versión
4. Versión de Laravel (php artisan --version)
5. Versión de PHP (php --version)
6. Último comando ejecutado
7. Pasos para reproducir el problema
```

---

## 📚 Documentos de Ayuda

- [COMPRAS_DOCUMENTACION.md](./COMPRAS_DOCUMENTACION.md) - Referencia técnica
- [INSTALAR_COMPRAS.md](./INSTALAR_COMPRAS.md) - Instalación
- [SQL_QUERIES_COMPRAS.md](./SQL_QUERIES_COMPRAS.md) - Consultas BD
- [CHECKLIST_COMPRAS.md](./CHECKLIST_COMPRAS.md) - Verificación

---

**Última actualización:** 21 de enero de 2026
