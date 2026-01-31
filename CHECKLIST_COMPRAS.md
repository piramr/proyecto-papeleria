# CHECKLIST POST-INSTALACIÓN - MÓDULO DE COMPRAS

## ✅ Verificación de Archivos Creados

### Modelos (2 archivos)
- [ ] `app/Models/Compra.php` existe
- [ ] `app/Models/CompraDetalle.php` existe
- [ ] Ambos modelos tienen relaciones correctas

### Controlador (1 archivo)
- [ ] `app/Http/Controllers/CompraController.php` existe
- [ ] Tiene 10 métodos públicos
- [ ] Métodos de validación privados

### Migraciones (1 archivo)
- [ ] `database/migrations/2026_01_21_000000_create_compras_table.php` existe
- [ ] Define tabla `compras` con 15 campos
- [ ] Define tabla `compra_detalles` con 6 campos
- [ ] Claves foráneas están correctas

### Vistas (4 archivos)
- [ ] `resources/views/admin/compras/index.blade.php` existe
- [ ] `resources/views/admin/compras/create.blade.php` existe
- [ ] `resources/views/admin/compras/edit.blade.php` existe
- [ ] `resources/views/admin/compras/show.blade.php` existe

### Seeders (2 archivos)
- [ ] `database/seeders/TipoPagoSeeder.php` existe
- [ ] `database/seeders/CompraSeeder.php` existe

### Rutas (1 archivo)
- [ ] `routes/web.php` importa `CompraController`
- [ ] Define 9 rutas de compras
- [ ] Rutas están en grupo `admin/compras`

### Documentación (3 archivos)
- [ ] `COMPRAS_DOCUMENTACION.md` existe
- [ ] `INSTALAR_COMPRAS.md` existe
- [ ] `RESUMEN_EJECUTIVO_COMPRAS.md` existe

---

## ✅ Verificación de Base de Datos

### Antes de ejecutar migraciones
- [ ] Base de datos creada
- [ ] Conexión a BD funciona

### Después de ejecutar migraciones
```bash
php artisan migrate
```
- [ ] Comando ejecutado sin errores
- [ ] Tabla `compras` creada en BD
- [ ] Tabla `compra_detalles` creada en BD
- [ ] Índices creados correctamente

### Verificar estructura de tablas
```sql
DESCRIBE compras;
DESCRIBE compra_detalles;
```
- [ ] Tabla `compras` tiene 15 campos
- [ ] Tabla `compra_detalles` tiene 6 campos
- [ ] Tipos de datos correctos
- [ ] Claves primarias definidas

### Seeders
```bash
php artisan db:seed --class=TipoPagoSeeder
```
- [ ] Tipos de pago insertados
- [ ] Al menos 4 registros en tabla `tipo_pagos`

---

## ✅ Verificación de Rutas

### Ver todas las rutas de compras
```bash
php artisan route:list | grep compras
```
- [ ] 9 rutas listadas
- [ ] Métodos HTTP correctos (GET, POST, PUT)
- [ ] Nombres de rutas con prefijo `compras.`

### Rutas esperadas:
```
GET    /admin/compras                              ✓
GET    /admin/compras/crear                        ✓
POST   /admin/compras                              ✓
GET    /admin/compras/{compra}                     ✓
GET    /admin/compras/{compra}/editar              ✓
PUT    /admin/compras/{compra}                     ✓
POST   /admin/compras/{compra}/recibir             ✓
POST   /admin/compras/{compra}/cancelar            ✓
GET    /admin/compras/productos-proveedor/{ruc}    ✓
```

---

## ✅ Verificación de Acceso

### Datos necesarios previos
- [ ] Al menos 1 Proveedor registrado en tabla `proveedores`
- [ ] Al menos 1 Producto registrado en tabla `productos`
- [ ] Al menos 1 Tipo de Pago registrado en tabla `tipo_pagos`
- [ ] Relación entre Proveedor y Producto en `productos_proveedores`

### Acceso a la aplicación
```bash
php artisan serve
```
- [ ] Servidor iniciado en `localhost:8000`
- [ ] Usuario autenticado (login exitoso)

### Prueba de URLs
- [ ] Accede a `http://localhost:8000/admin/compras` ✓
- [ ] Página carga sin errores 500 ✓
- [ ] Puedes ver el botón "Nueva Compra" ✓

---

## ✅ Funcionalidad Básica

### Crear Compra
- [ ] Click en "Nueva Compra" redirige a formulario
- [ ] Formulario carga los proveedores
- [ ] Puedes seleccionar un proveedor
- [ ] Al seleccionar proveedor, carga productos (AJAX)
- [ ] Puedes agregar productos dinámicamente
- [ ] El cálculo de totales funciona en tiempo real
- [ ] Puedes enviar el formulario exitosamente
- [ ] Se genera número de compra automáticamente
- [ ] Redirige a página de detalles

### Listar Compras
- [ ] Página `/admin/compras` carga correctamente
- [ ] Tabla muestra todas las compras
- [ ] Columnas: Nº, Fecha, Proveedor, Subtotal, IVA, Total, Estado
- [ ] Paginación funciona (si hay >15 registros)
- [ ] Botones de acción aparecen

### Ver Detalles
- [ ] Click en "Ver" abre detalles de compra
- [ ] Muestra información completa
- [ ] Tabla de productos con detalles
- [ ] Resumen de totales correcto
- [ ] Botones de acción contextuales

### Editar Compra
- [ ] Solo compras "pendiente" muestran botón editar
- [ ] Carga formulario con datos actuales
- [ ] Puedes modificar proveedor y productos
- [ ] Validaciones funcionan
- [ ] Se actualiza correctamente

### Marcar como Recibida
- [ ] Botón "Marcar como Recibida" aparece si estado = pendiente
- [ ] Modal de confirmación aparece
- [ ] Stock se actualiza tras confirmar
- [ ] Estado cambia a "recibida"
- [ ] No permite editar luego

### Cancelar Compra
- [ ] Botón "Cancelar Compra" aparece si estado = pendiente
- [ ] Modal pide razón de cancelación
- [ ] Estado cambia a "anulada"
- [ ] Registra observaciones

---

## ✅ Validaciones

### En Formularios
- [ ] Campo Proveedor requerido
- [ ] Campo Fecha requerido
- [ ] Campo Cantidad es numérico
- [ ] Campo Precio es decimal
- [ ] Mensaje de error claro si falta producto
- [ ] No deja guardar sin al menos 1 producto

### En Servidor
- [ ] Valida que proveedor existe
- [ ] Valida que productos existen
- [ ] Valida que todos los productos pertenecen al proveedor
- [ ] Mensaje de error clara si producto no pertenece

### Ejemplos de validación
```
✓ Intenta agregar producto de otro proveedor → Error
✓ Intenta guardar sin productos → Error
✓ Intenta editar compra recibida → Error
✓ Intenta marcar sin recibir sin cambios → OK
```

---

## ✅ Cálculos Matemáticos

### En Tiempo Real (JavaScript)
- [ ] Subtotal se actualiza al cambiar cantidad o precio
- [ ] IVA se calcula al 12% si producto tiene IVA
- [ ] Total = Subtotal + IVA
- [ ] Formato con separador de miles: 1.234,56

### En Base de Datos
- [ ] Subtotal en detalle = cantidad × precio
- [ ] Subtotal compra = suma de detalles
- [ ] IVA = suma de (detalle × 0.12 si tiene_iva)
- [ ] Total = subtotal + iva

### Verificación de cálculos
```
Producto A: 5 unidades × $10 = $50 (SIN IVA)
Producto B: 2 unidades × $20 = $40 (CON IVA)

Subtotal: $50 + $40 = $90
IVA: $40 × 0.12 = $4.80
Total: $90 + $4.80 = $94.80

✓ Correcto
```

---

## ✅ Integración con Otros Módulos

### Modelos relacionados
- [ ] Modelo `Proveedor` existe
- [ ] Modelo `Producto` existe
- [ ] Modelo `TipoPago` existe
- [ ] Relación `productos_proveedores` existe

### Actualización de Stock
- [ ] Al recibir compra, stock del producto aumenta
- [ ] Cantidad es la de la compra
- [ ] Se registra fecha de recepción

### Prueba de stock
```sql
-- Antes de recibir
SELECT cantidad_stock FROM productos WHERE id = 1;

-- Después de recibir compra
SELECT cantidad_stock FROM productos WHERE id = 1;
-- Debe ser cantidad_anterior + cantidad_comprada
```

---

## ✅ Errores Comunes y Soluciones

### Error: "Class CompraController not found"
```bash
# Solución: Actualiza autoload
composer dump-autoload
php artisan cache:clear
```

### Error: "SQLSTATE[42S02]: Table not found"
```bash
# Solución: Ejecuta migraciones
php artisan migrate
```

### Error: "Products not loading"
- [ ] Verifica tabla `productos_proveedores` tiene datos
- [ ] Verifica que `proveedor_ruc` coincide exactamente
- [ ] Abre consola del navegador (F12) para errores AJAX

### Error: "Stock not updating"
- [ ] Verifica que compra está en estado "pendiente"
- [ ] Verifica que tabla `productos` tiene columna `cantidad_stock`
- [ ] Revisa logs: `storage/logs/laravel.log`

### Página vacía / 500 error
```bash
# Solución: Revisa logs
tail -f storage/logs/laravel.log

# Limpia cache
php artisan cache:clear
php artisan route:clear
```

---

## ✅ Performance

### Optimizaciones implementadas
- [ ] Eager loading en index (con `with()`)
- [ ] Índices en tablas para búsquedas
- [ ] Paginación de 15 registros
- [ ] AJAX para cargar productos (sin refresco)

### Pruebas de rendimiento
- [ ] Listar 100 compras: < 1 segundo
- [ ] Crear compra con 10 productos: < 2 segundos
- [ ] Cargar productos de proveedor (AJAX): < 500ms

---

## ✅ Pruebas E2E (Manual)

### Escenario 1: Crear compra exitosa
1. [ ] Ir a `/admin/compras`
2. [ ] Click "Nueva Compra"
3. [ ] Seleccionar proveedor
4. [ ] Agregar 2-3 productos
5. [ ] Verificar cálculos
6. [ ] Guardar
7. [ ] Verificar número generado
8. [ ] Verificar estado = "pendiente"

### Escenario 2: Editar compra
1. [ ] Abrir compra pendiente
2. [ ] Click "Editar"
3. [ ] Cambiar cantidad de producto
4. [ ] Verificar recálculo de totales
5. [ ] Guardar
6. [ ] Verificar cambios en listado

### Escenario 3: Recibir compra
1. [ ] Abrir compra pendiente
2. [ ] Click "Marcar como Recibida"
3. [ ] Confirmar en modal
4. [ ] Verificar estado = "recibida"
5. [ ] Verificar que stock aumentó
6. [ ] Intentar editar (debe mostrar error)

### Escenario 4: Cancelar compra
1. [ ] Abrir compra pendiente
2. [ ] Click "Cancelar Compra"
3. [ ] Ingresar razón
4. [ ] Confirmar
5. [ ] Verificar estado = "anulada"
6. [ ] Verificar observaciones guardadas
7. [ ] Verificar que stock NO cambió

---

## ✅ Limpieza y Producción

### Antes de ir a producción
- [ ] Cambiar `APP_DEBUG=false` en `.env`
- [ ] Cambiar `APP_ENV=production` en `.env`
- [ ] Ejecutar `php artisan optimize`
- [ ] Ejecutar `php artisan cache:clear`
- [ ] Realizar backup de base de datos

### Documentación completada
- [ ] README actualizado con módulo de compras
- [ ] Documentación técnica disponible
- [ ] Manual de usuario disponible
- [ ] Logs configurados correctamente

---

## 📊 Resumen Final

```
✓ Archivos creados:     10
✓ Tablas creadas:        2
✓ Rutas creadas:         9
✓ Vistas creadas:        4
✓ Modelos creados:       2
✓ Métodos del controlador: 10
✓ Seeders creados:       2
✓ Documentos creados:    3

Total: 42 componentes desarrollados
```

---

## 🎉 ¡LISTO PARA USAR!

Si todas las casillas están marcadas ✓, el módulo de compras está completamente funcional y listo para producción.

**Próximo paso:** Consultar documentación si necesitas extensiones o modificaciones.

---

**Última actualización:** 21 de enero de 2026
**Estado:** ✅ COMPLETADO
