-- ============================================
-- GUÍA DE PRUEBAS EN pgAdmin
-- Procedimientos, Triggers y Funciones
-- Base de Datos: app_xpress
-- ============================================

-- ============================================
-- PASO 1: VERIFICAR QUE EXISTEN LOS OBJETOS
-- ============================================

-- Ver todas las funciones creadas
SELECT 
    routine_name, 
    routine_type,
    data_type as return_type
FROM information_schema.routines 
WHERE routine_schema = 'public'
AND routine_name LIKE 'fn_%' OR routine_name LIKE 'sp_%'
ORDER BY routine_name;

-- Ver todos los triggers
SELECT 
    trigger_name, 
    event_object_table as tabla,
    action_timing as momento,
    event_manipulation as evento
FROM information_schema.triggers 
WHERE trigger_schema = 'public'
ORDER BY event_object_table, trigger_name;

-- Ver tablas relacionadas
SELECT table_name 
FROM information_schema.tables 
WHERE table_schema = 'public'
AND (table_name LIKE '%producto%' 
    OR table_name LIKE '%auditoria%'
    OR table_name LIKE '%log%')
ORDER BY table_name;


-- ============================================
-- PRUEBAS DE FUNCIONES DE INVENTARIO
-- ============================================

-- ========== FUNCIÓN: fn_stock_disponible ==========
-- Obtener el stock de un producto específico
-- Primero, ver que productos existen:
SELECT id, nombre, cantidad_stock FROM productos LIMIT 5;

-- Ahora probar la función (cambiar el ID según tus datos):
SELECT fn_stock_disponible(1) as stock_producto_1;
SELECT fn_stock_disponible(2) as stock_producto_2;

-- Comparar con la tabla real:
SELECT id, nombre, cantidad_stock, fn_stock_disponible(id) as stock_funcion
FROM productos
LIMIT 10;


-- ========== FUNCIÓN: fn_obtener_precio_final ==========
-- Obtener precio final (con oferta si aplica)
SELECT id, nombre, precio_unitario, en_oferta, precio_oferta FROM productos LIMIT 5;

-- Probar función:
SELECT 
    id,
    nombre,
    precio_unitario,
    en_oferta,
    precio_oferta,
    fn_obtener_precio_final(id) as precio_final
FROM productos
LIMIT 10;

-- Caso específico:
SELECT fn_obtener_precio_final(1) as precio_final_producto_1;


-- ========== FUNCIÓN: fn_contar_productos_categoria ==========
-- Contar productos por categoría
-- Ver categorías disponibles:
SELECT id, nombre FROM categorias;

-- Probar función:
SELECT fn_contar_productos_categoria(1) as productos_en_categoria_1;

-- Ver todas las categorías con conteo:
SELECT 
    c.id,
    c.nombre,
    fn_contar_productos_categoria(c.id) as total_productos
FROM categorias c
ORDER BY c.nombre;


-- ========== FUNCIÓN: fn_valor_inventario_total ==========
-- Obtener el valor total del inventario
SELECT fn_valor_inventario_total() as valor_total_inventario;

-- Comparar con cálculo manual:
SELECT 
    SUM(cantidad_stock * precio_unitario) as suma_manual,
    fn_valor_inventario_total() as funcion_valor
FROM productos
WHERE deleted_at IS NULL;


-- ========== FUNCIÓN: fn_margen_ganancia ==========
-- Calcular margen de ganancia
SELECT fn_margen_ganancia(100, 150) as margen_50_porciento;
SELECT fn_margen_ganancia(100, 120) as margen_20_porciento;
SELECT fn_margen_ganancia(100, 100) as margen_0_porciento;

-- Probar con datos reales (productos con proveedores):
SELECT 
    p.id,
    p.nombre,
    pp.precio_costo,
    p.precio_unitario as precio_venta,
    fn_margen_ganancia(pp.precio_costo, p.precio_unitario) as margen
FROM productos p
JOIN producto_proveedores pp ON p.id = pp.producto_id
LIMIT 10;


-- ============================================
-- PRUEBAS DE PROCEDIMIENTOS DE INVENTARIO
-- ============================================

-- ========== FUNCIÓN: sp_productos_bajo_stock ==========
-- Obtener cantidad de productos bajo stock mínimo
SELECT sp_productos_bajo_stock() as productos_bajo_stock;

-- Ver qué productos están bajo stock:
SELECT 
    id, 
    nombre, 
    cantidad_stock, 
    stock_minimo,
    (cantidad_stock - stock_minimo) as diferencia
FROM productos
WHERE cantidad_stock < stock_minimo
AND deleted_at IS NULL
ORDER BY diferencia;


-- ========== FUNCIÓN: sp_valor_inventario_por_categoria ==========
-- Obtener valor de inventario agrupado por categoría
SELECT * FROM sp_valor_inventario_por_categoria();

-- Comparar con query manual:
SELECT 
    c.id,
    c.nombre,
    COUNT(p.id) as cantidad_productos,
    COALESCE(SUM(p.cantidad_stock), 0) as total_unidades,
    COALESCE(SUM(p.cantidad_stock * p.precio_unitario), 0) as valor_total
FROM categorias c
LEFT JOIN productos p ON c.id = p.categoria_id AND p.deleted_at IS NULL
GROUP BY c.id, c.nombre
ORDER BY valor_total DESC;


-- ========== PROCEDIMIENTO: sp_actualizar_stock ==========
-- IMPORTANTE: Este modifica datos, hacer backup antes!

-- Ver stock actual antes de actualizar:
SELECT id, nombre, cantidad_stock 
FROM productos 
WHERE id = 1;

-- Ver tabla de movimientos antes:
SELECT COUNT(*) as movimientos_antes FROM log_movimiento_inventario;

-- Ejecutar procedimiento (ENTRADA de 50 unidades):
CALL sp_actualizar_stock(
    1,              -- producto_id
    50,             -- cantidad
    'entrada',      -- tipo
    'Prueba desde pgAdmin - Entrada inicial',  -- razon
    1               -- user_id (debe existir en tabla users)
);

-- Verificar que aumentó el stock:
SELECT id, nombre, cantidad_stock 
FROM productos 
WHERE id = 1;

-- Verificar que se registró el movimiento:
SELECT * FROM log_movimiento_inventario 
WHERE producto_id = 1 
ORDER BY created_at DESC 
LIMIT 5;

-- Ejecutar procedimiento (SALIDA de 10 unidades):
CALL sp_actualizar_stock(
    1,              -- producto_id
    10,             -- cantidad
    'salida',       -- tipo
    'Prueba desde pgAdmin - Venta',  -- razon
    1               -- user_id
);

-- Ver stock después de salida:
SELECT id, nombre, cantidad_stock 
FROM productos 
WHERE id = 1;

-- Ver todos los movimientos del producto:
SELECT 
    id,
    tipo_movimiento,
    cantidad,
    razon,
    created_at
FROM log_movimiento_inventario 
WHERE producto_id = 1 
ORDER BY created_at DESC;

-- PROBAR ERROR: Intentar sacar más stock del disponible
-- Esto debería dar error "Stock insuficiente"
CALL sp_actualizar_stock(
    1,              -- producto_id
    99999,          -- cantidad enorme
    'salida',       -- tipo
    'Prueba de error',
    1
);
-- Debe fallar y no modificar nada


-- ========== PROCEDIMIENTO: sp_actualizar_precio_masivo ==========
-- CUIDADO: Modifica precios de productos!

-- Ver precios antes:
SELECT id, nombre, precio_unitario, categoria_id
FROM productos
WHERE categoria_id = 1
LIMIT 5;

-- Aumentar 10% los precios de categoría 1:
CALL sp_actualizar_precio_masivo(
    10,     -- porcentaje (10%)
    1       -- categoria_id
);

-- Ver precios después:
SELECT id, nombre, precio_unitario, categoria_id
FROM productos
WHERE categoria_id = 1
LIMIT 5;

-- Restaurar precios (disminuir 9.09% para volver al original):
CALL sp_actualizar_precio_masivo(
    -9.09,  -- porcentaje negativo
    1       -- categoria_id
);


-- ========== FUNCIÓN: sp_productos_por_proveedor ==========
-- Estadísticas de productos por proveedor
SELECT * FROM sp_productos_por_proveedor()
ORDER BY cantidad_productos DESC;

-- Ver detalle de un proveedor específico:
SELECT 
    p.nombre as producto,
    pp.precio_costo,
    p.precio_unitario,
    p.cantidad_stock
FROM productos p
JOIN producto_proveedores pp ON p.id = pp.producto_id
WHERE pp.proveedor_ruc = (
    SELECT ruc FROM proveedores LIMIT 1
);


-- ============================================
-- PRUEBAS DE FUNCIONES DE AUDITORÍA
-- ============================================

-- ========== FUNCIÓN: fn_ultima_auditoria ==========
-- Ver auditorías disponibles:
SELECT DISTINCT entidad, recurso_id 
FROM auditoria_datos 
ORDER BY entidad 
LIMIT 10;

-- Probar función:
SELECT fn_ultima_auditoria('productos', '1') as ultima_auditoria_producto_1;

-- Ver todas las auditorías de un producto:
SELECT id, timestamp, tipo_operacion, campo, valor_original, valor_nuevo
FROM auditoria_datos
WHERE entidad = 'productos' AND recurso_id = '1'
ORDER BY timestamp DESC;


-- ========== FUNCIÓN: fn_cambios_por_usuario ==========
-- Ver usuarios disponibles:
SELECT id, name FROM users LIMIT 5;

-- Contar cambios de hoy de un usuario:
SELECT fn_cambios_por_usuario(1, CURRENT_DATE) as cambios_usuario_1_hoy;

-- Ver cambios de todos los usuarios hoy:
SELECT 
    u.id,
    u.name,
    fn_cambios_por_usuario(u.id, CURRENT_DATE) as cambios_hoy
FROM users u
ORDER BY cambios_hoy DESC;


-- ========== FUNCIÓN: fn_usuario_activo ==========
-- Verificar si usuarios están activos:
SELECT 
    id,
    name,
    email,
    email_verified_at,
    fn_usuario_activo(id) as esta_activo
FROM users
LIMIT 10;


-- ========== FUNCIÓN: fn_cambios_criticos_count ==========
-- Contar cambios críticos del día:
SELECT fn_cambios_criticos_count() as cambios_criticos_hoy;

-- Ver qué cambios son considerados críticos:
SELECT tipo_operacion, COUNT(*) as cantidad
FROM auditoria_datos
WHERE tipo_operacion IN ('DELETE', 'UPDATE_CRITICO')
AND DATE(timestamp) = CURRENT_DATE
GROUP BY tipo_operacion;


-- ============================================
-- PRUEBAS DE PROCEDIMIENTOS DE AUDITORÍA
-- ============================================

-- ========== PROCEDIMIENTO: sp_registrar_auditoria ==========
-- Registrar manualmente una auditoría de prueba:
CALL sp_registrar_auditoria(
    1,                          -- user_id
    'UPDATE',                   -- tipo_operacion
    'productos',                -- entidad
    '1',                        -- recurso_id
    'precio_unitario',          -- campo
    '100.00',                   -- valor_viejo
    '120.00'                    -- valor_nuevo
);

-- Verificar que se registró:
SELECT * FROM auditoria_datos 
ORDER BY timestamp DESC 
LIMIT 1;


-- ========== FUNCIÓN: sp_reporte_auditoria ==========
-- Generar reporte de auditoría de los últimos 7 días:
SELECT * FROM sp_reporte_auditoria(
    CURRENT_DATE - INTERVAL '7 days',
    CURRENT_DATE
)
LIMIT 20;

-- Reporte del mes actual:
SELECT * FROM sp_reporte_auditoria(
    DATE_TRUNC('month', CURRENT_DATE)::DATE,
    CURRENT_DATE
)
ORDER BY fecha DESC;

-- Resumen por tipo de operación:
SELECT 
    tipo_operacion,
    COUNT(*) as cantidad,
    COUNT(DISTINCT usuario) as usuarios_distintos
FROM sp_reporte_auditoria(
    CURRENT_DATE - INTERVAL '30 days',
    CURRENT_DATE
)
GROUP BY tipo_operacion
ORDER BY cantidad DESC;


-- ========== FUNCIÓN: sp_historial_cambios ==========
-- Ver historial completo de un producto:
SELECT * FROM sp_historial_cambios('productos', '1')
ORDER BY fecha DESC;

-- Ver cambios de un proveedor:
SELECT * FROM sp_historial_cambios('proveedores', '1234567890')
ORDER BY fecha DESC;

-- Ver cambios de una categoría:
SELECT * FROM sp_historial_cambios('categorias', '1')
ORDER BY fecha DESC;


-- ========== FUNCIÓN: sp_validar_usuario ==========
-- Validar integridad de un usuario:
SELECT * FROM sp_validar_usuario(1);

-- Ver estadísticas de todos los usuarios:
SELECT 
    u.id,
    vul.*
FROM users u
CROSS JOIN LATERAL sp_validar_usuario(u.id) vul
ORDER BY total_cambios DESC
LIMIT 10;


-- ========== FUNCIÓN: sp_cambios_criticos ==========
-- Ver cambios críticos del día:
SELECT * FROM sp_cambios_criticos()
ORDER BY fecha DESC;

-- Resumen de cambios críticos:
SELECT 
    tipo_operacion,
    entidad,
    COUNT(*) as cantidad
FROM sp_cambios_criticos()
GROUP BY tipo_operacion, entidad
ORDER BY cantidad DESC;


-- ========== PROCEDIMIENTO: sp_limpiar_logs_antiguos ==========
-- CUIDADO: Esto ELIMINA datos permanentemente!

-- Ver cuántos registros hay antes:
SELECT 'log_login' as tabla, COUNT(*) as registros FROM log_login
UNION ALL
SELECT 'log_sistema', COUNT(*) FROM log_sistema
UNION ALL
SELECT 'auditoria_datos', COUNT(*) FROM auditoria_datos;

-- Ejecutar limpieza de logs mayores a 90 días
-- DESCOMENTAR SOLO SI QUIERES ELIMINAR DATOS:
-- CALL sp_limpiar_logs_antiguos(90);

-- Ver cuántos registros quedan después:
SELECT 'log_login' as tabla, COUNT(*) as registros FROM log_login
UNION ALL
SELECT 'log_sistema', COUNT(*) FROM log_sistema
UNION ALL
SELECT 'auditoria_datos', COUNT(*) FROM auditoria_datos;


-- ============================================
-- PRUEBAS DE TRIGGERS AUTOMÁTICOS
-- ============================================

-- ========== TRIGGER: tr_audit_producto_insert ==========
-- Insertar un producto nuevo y verificar auditoría:

-- Contar auditorías antes:
SELECT COUNT(*) FROM auditoria_datos WHERE entidad = 'productos';

-- Insertar producto (ajusta los valores según tu esquema):
INSERT INTO productos (
    codigo_barras, nombre, caracteristicas, cantidad_stock,
    stock_minimo, stock_maximo, tiene_iva, precio_unitario,
    marca, categoria_id, created_at, updated_at
) VALUES (
    'TEST123',
    'Producto de Prueba Trigger',
    'Prueba desde pgAdmin',
    100,
    10,
    500,
    true,
    50.00,
    'Test Brand',
    1,
    NOW(),
    NOW()
);

-- Verificar que se creó el producto:
SELECT * FROM productos WHERE codigo_barras = 'TEST123';

-- Verificar que el TRIGGER creó la auditoría automáticamente:
SELECT * FROM auditoria_datos 
WHERE entidad = 'productos' 
ORDER BY timestamp DESC 
LIMIT 1;

-- Limpiar:
DELETE FROM productos WHERE codigo_barras = 'TEST123';


-- ========== TRIGGER: tr_audit_producto_update ==========
-- Actualizar precio y verificar auditoría automática:

-- Ver producto antes:
SELECT id, nombre, precio_unitario FROM productos WHERE id = 1;

-- Actualizar precio (ESTO DISPARA EL TRIGGER):
UPDATE productos 
SET precio_unitario = precio_unitario + 10
WHERE id = 1;

-- Ver producto después:
SELECT id, nombre, precio_unitario FROM productos WHERE id = 1;

-- Verificar auditoría del cambio de precio:
SELECT * FROM auditoria_datos 
WHERE entidad = 'productos' 
AND recurso_id = '1'
AND campo = 'precio_unitario'
ORDER BY timestamp DESC 
LIMIT 2;

-- Actualizar stock (TAMBIÉN DISPARA EL TRIGGER):
UPDATE productos 
SET cantidad_stock = cantidad_stock + 5
WHERE id = 1;

-- Verificar auditoría del cambio de stock:
SELECT * FROM auditoria_datos 
WHERE entidad = 'productos' 
AND recurso_id = '1'
AND campo = 'cantidad_stock'
ORDER BY timestamp DESC 
LIMIT 2;


-- ========== TRIGGER: tr_validar_stock_producto ==========
-- Intentar poner stock negativo (debería fallar):

-- Esto DEBE fallar con error "No se puede establecer stock negativo":
UPDATE productos 
SET cantidad_stock = -10
WHERE id = 1;
-- No se ejecutará

-- Este debería funcionar:
UPDATE productos 
SET cantidad_stock = 0
WHERE id = 1;


-- ========== TRIGGER: Stock bajo mínimo ==========
-- Probar alerta cuando stock baja del mínimo:

-- Ver producto y su stock mínimo:
SELECT id, nombre, cantidad_stock, stock_minimo 
FROM productos 
WHERE id = 1;

-- Ver logs antes:
SELECT COUNT(*) FROM log_sistema WHERE mensaje LIKE '%stock%';

-- Actualizar stock por debajo del mínimo:
UPDATE productos 
SET cantidad_stock = stock_minimo - 1
WHERE id = 1;

-- Verificar que se creó el log de alerta:
SELECT * FROM log_sistema 
WHERE mensaje LIKE '%stock%'
ORDER BY timestamp DESC 
LIMIT 1;


-- ========== TRIGGER: tr_audit_proveedor_insert ==========
-- Insertar proveedor y verificar auditoría:

INSERT INTO proveedores (
    ruc, nombre, email, telefono_principal,
    created_at, updated_at
) VALUES (
    'TEST999999',
    'Proveedor Prueba Trigger',
    'test@trigger.com',
    '0999999999',
    NOW(),
    NOW()
);

-- Verificar auditoría automática:
SELECT * FROM auditoria_datos 
WHERE entidad = 'proveedores' 
AND recurso_id = 'TEST999999'
ORDER BY timestamp DESC;

-- Limpiar:
DELETE FROM proveedores WHERE ruc = 'TEST999999';


-- ========== TRIGGER: tr_audit_proveedor_update ==========
-- Actualizar email de proveedor:
SELECT ruc, nombre, email FROM proveedores LIMIT 1;

UPDATE proveedores 
SET email = 'nuevo_email@test.com'
WHERE ruc = (SELECT ruc FROM proveedores LIMIT 1);

-- Ver auditoría del cambio:
SELECT * FROM auditoria_datos 
WHERE entidad = 'proveedores' 
AND campo = 'email'
ORDER BY timestamp DESC 
LIMIT 1;


-- ========== TRIGGER: tr_audit_categoria_insert ==========
-- Insertar categoría:
INSERT INTO categorias (nombre, created_at, updated_at)
VALUES ('Categoría Prueba Trigger', NOW(), NOW());

-- Ver auditoría:
SELECT * FROM auditoria_datos 
WHERE entidad = 'categorias'
ORDER BY timestamp DESC 
LIMIT 1;

-- Limpiar:
DELETE FROM categorias WHERE nombre = 'Categoría Prueba Trigger';


-- ============================================
-- CONSULTAS ÚTILES PARA MONITOREO
-- ============================================

-- Dashboard de auditoría (últimas 24 horas):
SELECT 
    DATE_TRUNC('hour', timestamp) as hora,
    tipo_operacion,
    COUNT(*) as cantidad
FROM auditoria_datos
WHERE timestamp > NOW() - INTERVAL '24 hours'
GROUP BY hora, tipo_operacion
ORDER BY hora DESC, cantidad DESC;

-- Top usuarios más activos:
SELECT 
    u.name,
    COUNT(ad.id) as total_cambios,
    COUNT(DISTINCT ad.entidad) as entidades_modificadas,
    MAX(ad.timestamp) as ultimo_cambio
FROM users u
JOIN auditoria_datos ad ON u.id = ad.user_id
WHERE ad.timestamp > NOW() - INTERVAL '7 days'
GROUP BY u.id, u.name
ORDER BY total_cambios DESC
LIMIT 10;

-- Productos con más movimientos:
SELECT 
    p.nombre,
    COUNT(lmi.id) as total_movimientos,
    SUM(CASE WHEN lmi.tipo_movimiento = 'entrada' THEN lmi.cantidad ELSE 0 END) as total_entradas,
    SUM(CASE WHEN lmi.tipo_movimiento = 'salida' THEN lmi.cantidad ELSE 0 END) as total_salidas,
    p.cantidad_stock as stock_actual
FROM productos p
JOIN log_movimiento_inventario lmi ON p.id = lmi.producto_id
GROUP BY p.id, p.nombre
ORDER BY total_movimientos DESC
LIMIT 10;

-- Valor de inventario por categoría con alertas:
SELECT 
    c.nombre as categoria,
    COUNT(p.id) as productos,
    SUM(p.cantidad_stock) as unidades,
    SUM(p.cantidad_stock * p.precio_unitario) as valor_total,
    COUNT(CASE WHEN p.cantidad_stock < p.stock_minimo THEN 1 END) as productos_bajo_stock
FROM categorias c
LEFT JOIN productos p ON c.id = p.categoria_id AND p.deleted_at IS NULL
GROUP BY c.id, c.nombre
ORDER BY valor_total DESC;

-- Log de sistema reciente:
SELECT 
    ls.timestamp,
    ln.nombre as nivel,
    ls.mensaje
FROM log_sistema ls
JOIN log_nivel ln ON ls.nivel_log_id = ln.id
ORDER BY ls.timestamp DESC
LIMIT 20;


-- ============================================
-- LIMPIEZA DE DATOS DE PRUEBA
-- ============================================

-- EJECUTAR SOLO SI QUIERES LIMPIAR LAS PRUEBAS:

-- Eliminar movimientos de prueba:
-- DELETE FROM log_movimiento_inventario WHERE razon LIKE '%Prueba desde pgAdmin%';

-- Eliminar auditorías de prueba:
-- DELETE FROM auditoria_datos WHERE session_id LIKE '%prueba%';

-- Eliminar productos de prueba:
-- DELETE FROM productos WHERE codigo_barras LIKE 'TEST%';

-- Eliminar proveedores de prueba:
-- DELETE FROM proveedores WHERE ruc LIKE 'TEST%';


-- ============================================
-- FIN DE LAS PRUEBAS
-- ============================================

-- Para ver resumen de todo lo creado:
SELECT 
    'Funciones' as tipo,
    COUNT(*) as cantidad
FROM information_schema.routines 
WHERE routine_schema = 'public' 
AND routine_name LIKE 'fn_%'

UNION ALL

SELECT 
    'Procedimientos',
    COUNT(*)
FROM information_schema.routines 
WHERE routine_schema = 'public' 
AND routine_name LIKE 'sp_%'

UNION ALL

SELECT 
    'Triggers',
    COUNT(*)
FROM information_schema.triggers 
WHERE trigger_schema = 'public'
AND trigger_name LIKE 'tr_%';

-- ¡Felicitaciones! Has completado las pruebas de todos los objetos de base de datos.
