# CONSULTAS SQL ÚTILES - MÓDULO DE COMPRAS

## 📋 Verificar Estructura de Tablas

### Ver estructura de tabla compras
```sql
DESCRIBE compras;
-- o
SHOW CREATE TABLE compras;
```

**Resultado esperado:**
- 15 campos
- Claves primarias: id
- Claves foráneas: proveedor_ruc, usuario_id, tipo_pago_id

### Ver estructura de tabla compra_detalles
```sql
DESCRIBE compra_detalles;
```

**Resultado esperado:**
- 6 campos
- Claves foráneas: compra_id, producto_id

---

## 🔍 Consultas de Verificación

### Verificar que las tablas existen
```sql
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'nombre_base_datos' 
AND TABLE_NAME IN ('compras', 'compra_detalles');
```

### Ver todas las compras registradas
```sql
SELECT 
    id,
    numero_compra,
    fecha_compra,
    proveedor_ruc,
    total,
    estado
FROM compras
ORDER BY fecha_compra DESC;
```

### Ver compras por proveedor
```sql
SELECT 
    c.numero_compra,
    c.fecha_compra,
    p.nombre as proveedor,
    c.total,
    c.estado
FROM compras c
JOIN proveedores p ON c.proveedor_ruc = p.ruc
WHERE c.proveedor_ruc = '1234567890123'
ORDER BY c.fecha_compra DESC;
```

### Ver detalles de una compra específica
```sql
SELECT 
    cd.producto_id,
    pr.nombre,
    cd.cantidad,
    cd.precio_unitario,
    cd.subtotal
FROM compra_detalles cd
JOIN productos pr ON cd.producto_id = pr.id
WHERE cd.compra_id = 1;
```

### Ver resumen de compras por estado
```sql
SELECT 
    estado,
    COUNT(*) as cantidad,
    SUM(total) as total_monto
FROM compras
GROUP BY estado;
```

---

## 💰 Análisis de Compras

### Total gastado por proveedor
```sql
SELECT 
    p.nombre as proveedor,
    COUNT(c.id) as cantidad_compras,
    SUM(c.total) as total_gastado,
    AVG(c.total) as promedio_compra
FROM compras c
JOIN proveedores p ON c.proveedor_ruc = p.ruc
GROUP BY c.proveedor_ruc, p.nombre
ORDER BY total_gastado DESC;
```

### Compras del último mes
```sql
SELECT 
    numero_compra,
    fecha_compra,
    proveedor_ruc,
    subtotal,
    iva,
    total
FROM compras
WHERE fecha_compra >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
ORDER BY fecha_compra DESC;
```

### Compras pendientes (sin recibir)
```sql
SELECT 
    c.numero_compra,
    c.fecha_compra,
    p.nombre as proveedor,
    c.total,
    u.name as usuario
FROM compras c
JOIN proveedores p ON c.proveedor_ruc = p.ruc
JOIN users u ON c.usuario_id = u.id
WHERE c.estado = 'pendiente'
ORDER BY c.fecha_compra;
```

### Compras recibidas con stock actualizado
```sql
SELECT 
    c.numero_compra,
    c.fecha_recepcion,
    p.nombre as proveedor,
    COUNT(cd.id) as cantidad_productos,
    SUM(cd.cantidad) as total_articulos,
    c.total
FROM compras c
JOIN proveedores p ON c.proveedor_ruc = p.ruc
LEFT JOIN compra_detalles cd ON c.id = cd.compra_id
WHERE c.estado = 'recibida'
GROUP BY c.id, c.numero_compra, c.fecha_recepcion, p.nombre, c.total
ORDER BY c.fecha_recepcion DESC;
```

---

## 📊 Análisis de Productos Comprados

### Productos más comprados
```sql
SELECT 
    pr.id,
    pr.nombre,
    SUM(cd.cantidad) as total_cantidad,
    COUNT(DISTINCT cd.compra_id) as num_compras,
    AVG(cd.precio_unitario) as precio_promedio
FROM compra_detalles cd
JOIN productos pr ON cd.producto_id = pr.id
GROUP BY cd.producto_id, pr.id, pr.nombre
ORDER BY total_cantidad DESC
LIMIT 10;
```

### Productos de un proveedor en las últimas compras
```sql
SELECT DISTINCT
    pr.id,
    pr.nombre,
    pp.precio_costo
FROM productos pr
JOIN productos_proveedores pp ON pr.id = pp.producto_id
WHERE pp.proveedor_ruc = '1234567890123'
ORDER BY pr.nombre;
```

### Historial de precios de compra de un producto
```sql
SELECT 
    c.numero_compra,
    c.fecha_compra,
    p.nombre as proveedor,
    cd.cantidad,
    cd.precio_unitario,
    cd.subtotal
FROM compra_detalles cd
JOIN compras c ON cd.compra_id = c.id
JOIN proveedores p ON c.proveedor_ruc = p.ruc
WHERE cd.producto_id = 1
ORDER BY c.fecha_compra DESC;
```

---

## 🔧 Mantenimiento de Datos

### Actualizar estado de compra
```sql
UPDATE compras 
SET estado = 'recibida', 
    fecha_recepcion = NOW()
WHERE id = 1 AND estado = 'pendiente';
```

### Registrar cancelación de compra
```sql
UPDATE compras 
SET estado = 'anulada', 
    observaciones = 'Razón de cancelación aquí'
WHERE id = 1 AND estado = 'pendiente';
```

### Recalcular totales de una compra (si hay inconsistencias)
```sql
UPDATE compras c
SET 
    subtotal = (
        SELECT COALESCE(SUM(subtotal), 0)
        FROM compra_detalles
        WHERE compra_id = c.id
    ),
    iva = (
        SELECT COALESCE(SUM(
            CASE 
                WHEN (SELECT tiene_iva FROM productos WHERE id = cd.producto_id) = 1 
                THEN cd.subtotal * 0.12 
                ELSE 0 
            END
        ), 0)
        FROM compra_detalles cd
        WHERE cd.compra_id = c.id
    )
WHERE id = 1;

-- Luego actualizar total
UPDATE compras 
SET total = subtotal + iva 
WHERE id = 1;
```

---

## 🚨 Verificaciones de Integridad

### Verificar que no hay detalles huérfanos
```sql
SELECT 
    cd.id,
    cd.compra_id,
    cd.producto_id
FROM compra_detalles cd
LEFT JOIN compras c ON cd.compra_id = c.id
WHERE c.id IS NULL;

-- Resultado esperado: 0 registros
```

### Verificar relaciones con proveedores
```sql
SELECT 
    c.id,
    c.numero_compra,
    c.proveedor_ruc
FROM compras c
LEFT JOIN proveedores p ON c.proveedor_ruc = p.ruc
WHERE p.ruc IS NULL;

-- Resultado esperado: 0 registros
```

### Verificar que todos los productos existen
```sql
SELECT 
    cd.id,
    cd.producto_id
FROM compra_detalles cd
LEFT JOIN productos p ON cd.producto_id = p.id
WHERE p.id IS NULL;

-- Resultado esperado: 0 registros
```

### Verificar que todos los usuarios existen
```sql
SELECT 
    c.id,
    c.usuario_id
FROM compras c
LEFT JOIN users u ON c.usuario_id = u.id
WHERE u.id IS NULL;

-- Resultado esperado: 0 registros
```

---

## 📈 Estadísticas Útiles

### Dashboard de compras
```sql
SELECT 
    CONCAT(
        'Total compras: ', COUNT(*), ' | ',
        'Pendientes: ', SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END), ' | ',
        'Recibidas: ', SUM(CASE WHEN estado = 'recibida' THEN 1 ELSE 0 END), ' | ',
        'Total gastado: $', ROUND(SUM(total), 2)
    ) as estadisticas
FROM compras;
```

### Promedio de días entre compra y recepción
```sql
SELECT 
    AVG(DATEDIFF(fecha_recepcion, fecha_compra)) as dias_promedio,
    MIN(DATEDIFF(fecha_recepcion, fecha_compra)) as minimo,
    MAX(DATEDIFF(fecha_recepcion, fecha_compra)) as maximo
FROM compras
WHERE estado = 'recibida' AND fecha_recepcion IS NOT NULL;
```

### Variación de precios de productos
```sql
SELECT 
    cd.producto_id,
    pr.nombre,
    MIN(cd.precio_unitario) as precio_minimo,
    MAX(cd.precio_unitario) as precio_maximo,
    AVG(cd.precio_unitario) as precio_promedio,
    MAX(cd.precio_unitario) - MIN(cd.precio_unitario) as variacion
FROM compra_detalles cd
JOIN productos pr ON cd.producto_id = pr.id
GROUP BY cd.producto_id, pr.id, pr.nombre
HAVING COUNT(*) > 1
ORDER BY variacion DESC;
```

---

## 🔐 Permisos y Seguridad

### Usuarios que crearon compras
```sql
SELECT DISTINCT
    u.id,
    u.name,
    u.email,
    COUNT(c.id) as compras_creadas,
    SUM(c.total) as monto_total
FROM users u
JOIN compras c ON u.id = c.usuario_id
GROUP BY u.id, u.name, u.email
ORDER BY compras_creadas DESC;
```

### Auditoría: Compras creadas en una fecha
```sql
SELECT 
    c.id,
    c.numero_compra,
    DATE(c.created_at) as fecha_creacion,
    c.estado,
    u.name as usuario,
    c.total
FROM compras c
JOIN users u ON c.usuario_id = u.id
WHERE DATE(c.created_at) = '2026-01-21'
ORDER BY c.created_at DESC;
```

---

## 🗑️ Limpieza de Datos (¡CUIDADO!)

### Eliminar compras de prueba (SOLO ANTES DE PRODUCCIÓN)
```sql
-- ADVERTENCIA: Esto borra datos, usar solo con cuidado
DELETE FROM compra_detalles WHERE compra_id IN (
    SELECT id FROM compras WHERE numero_compra LIKE 'TEST-%'
);

DELETE FROM compras WHERE numero_compra LIKE 'TEST-%';
```

### Restaurar stock si hay inconsistencias
```sql
-- Si el stock se modificó incorrectamente al recibir una compra
UPDATE productos p
SET p.cantidad_stock = p.cantidad_stock - (
    SELECT SUM(cd.cantidad)
    FROM compra_detalles cd
    JOIN compras c ON cd.compra_id = c.id
    WHERE cd.producto_id = p.id AND c.id = 1
)
WHERE p.id IN (
    SELECT cd.producto_id
    FROM compra_detalles cd
    WHERE cd.compra_id = 1
);
```

---

## 📝 Notas Importantes

1. **Backups:** Siempre hacer backup antes de ejecutar UPDATE o DELETE
2. **Transacciones:** Para operaciones múltiples usar `BEGIN; ... COMMIT;`
3. **Índices:** Asegúrate que existen en columnas de búsqueda frecuente
4. **Triggers:** Considera agregar triggers para auditoría automática
5. **Foreign Keys:** Verifica que los `FOREIGN KEY CHECKS` estén habilitados

---

## 🔍 Diagnóstico Rápido

```sql
-- Ejecuta esta consulta para un diagnóstico general
SELECT 
    (SELECT COUNT(*) FROM compras) as total_compras,
    (SELECT COUNT(*) FROM compra_detalles) as total_detalles,
    (SELECT SUM(total) FROM compras) as monto_total,
    (SELECT COUNT(*) FROM compras WHERE estado = 'pendiente') as compras_pendientes,
    (SELECT COUNT(*) FROM compras WHERE estado = 'recibida') as compras_recibidas,
    (SELECT COUNT(*) FROM proveedores) as total_proveedores,
    (SELECT COUNT(*) FROM productos) as total_productos;
```

---

**Última actualización:** 21 de enero de 2026
