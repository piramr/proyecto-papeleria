# 📚 IMPLEMENTACIÓN DE PROCEDIMIENTOS, TRIGGERS Y FUNCIONES

## ✅ ESTADO DE IMPLEMENTACIÓN

Todos los objetos de base de datos están **ACTIVOS Y FUNCIONANDO** en la aplicación.

---

## 🔧 OBJETOS DE BASE DE DATOS CREADOS

### 📦 INVENTARIO (PostgreSQL)

#### **Funciones**
- ✅ `fn_stock_disponible(producto_id)` - Obtener stock actual
- ✅ `fn_obtener_precio_final(producto_id)` - Calcular precio con ofertas
- ✅ `fn_contar_productos_categoria(categoria_id)` - Contar productos por categoría
- ✅ `fn_valor_inventario_total()` - Valor total del inventario
- ✅ `fn_margen_ganancia(precio_costo, precio_venta)` - Calcular margen

#### **Procedimientos**
- ✅ `sp_actualizar_stock(producto_id, cantidad, tipo, razon, user_id)` - Actualizar stock con log
- ✅ `sp_actualizar_precio_masivo(porcentaje, categoria_id)` - Actualizar precios masivamente
- ✅ `sp_productos_bajo_stock()` - Productos bajo stock mínimo
- ✅ `sp_valor_inventario_por_categoria()` - Valor agrupado por categoría
- ✅ `sp_productos_por_proveedor()` - Estadísticas por proveedor

#### **Triggers Automáticos**
- ✅ `tr_audit_producto_insert` - Audita inserciones de productos
- ✅ `tr_audit_producto_update` - Audita cambios de precio y stock
- ✅ `tr_validar_stock_producto` - Valida stock negativo y alertas

### 📊 AUDITORÍA (PostgreSQL)

#### **Funciones**
- ✅ `fn_ultima_auditoria(entidad, recurso_id)` - Última auditoría de recurso
- ✅ `fn_cambios_por_usuario(user_id, fecha)` - Cambios por usuario
- ✅ `fn_usuario_activo(user_id)` - Validar usuario activo
- ✅ `fn_cambios_criticos_count()` - Conteo de cambios críticos

#### **Procedimientos**
- ✅ `sp_registrar_auditoria(...)` - Registrar cambio en auditoría
- ✅ `sp_limpiar_logs_antiguos(dias_retencion)` - Limpieza automática
- ✅ `sp_reporte_auditoria(fecha_inicio, fecha_fin)` - Reporte de auditoría
- ✅ `sp_historial_cambios(entidad, recurso_id)` - Historial de un recurso
- ✅ `sp_validar_usuario(user_id)` - Validar integridad de usuario
- ✅ `sp_cambios_criticos()` - Cambios críticos del día

#### **Triggers Automáticos**
- ✅ `tr_audit_proveedor_insert` - Audita inserciones de proveedores
- ✅ `tr_audit_proveedor_update` - Audita cambios de proveedores
- ✅ `tr_audit_categoria_insert` - Audita inserciones de categorías

---

## 🎯 SERVICIOS INTEGRADOS

### **InventarioService** (`app/Services/InventarioService.php`)
```php
// Ejemplo de uso:
$inventarioService = app(\App\Services\InventarioService::class);

// Obtener stock
$stock = $inventarioService->obtenerStockDisponible($productoId);

// Movimiento de inventario
$inventarioService->actualizarStock(
    productoId: 1,
    cantidad: 10,
    tipo: 'entrada',
    razon: 'Compra a proveedor',
    userId: auth()->id()
);

// Reportes
$valorTotal = $inventarioService->valorInventarioTotal();
$bajoStock = $inventarioService->obtenerProductosBajoStock();
$porCategoria = $inventarioService->valorInventarioPorCategoria();
```

### **AuditoriaService** (`app/Services/Auditoria/AuditoriaService.php`)
```php
// Método tradicional (sin procedures)
AuditoriaService::registrarAuditoriaDatos([...]);

// Métodos con procedures (nuevos)
$ultimaAud = AuditoriaService::obtenerUltimaAuditoria('productos', '123');
$cambios = AuditoriaService::contarCambiosPorUsuario(1, '2026-02-10');
$reporte = AuditoriaService::generarReporte('2026-02-01', '2026-02-10');
$historial = AuditoriaService::obtenerHistorialCambios('productos', '123');
```

---

## 🔗 CONTROLADORES INTEGRADOS

### **ProductoController** (ACTIVO)

#### **Endpoints Nuevos:**

**1. Movimiento de Stock**
```
POST /admin/productos/{id}/movimiento-stock
```
```json
{
    "cantidad": 50,
    "tipo": "entrada",
    "razon": "Compra a proveedor XYZ"
}
```

**2. Análisis de Inventario**
```
GET /admin/productos/{id}/analisis
```
Retorna:
- Stock disponible
- Precio final con ofertas
- Movimientos recientes
- Margen de ganancia

**3. Reporte General**
```
GET /admin/productos/reporte/general
```
Retorna:
- Valor total inventario
- Inventario por categoría
- Productos bajo stock
- Productos por proveedor

---

## 🤖 OBSERVERS AUTOMÁTICOS

### **ProductoObserver** (`app/Observers/ProductoObserver.php`)
- ✅ Registra automáticamente en auditoría al crear productos
- ✅ Audita cambios de precio y stock
- ✅ Genera logs de sistema para cambios críticos
- ✅ Alertas de stock bajo mínimo

### **ProveedorObserver** (`app/Observers/ProveedorObserver.php`)
- ✅ Audita inserciones de proveedores
- ✅ Audita cambios de información
- ✅ Logs de sistema automáticos

### **CategoriaObserver** (`app/Observers/CategoriaObserver.php`)
- ✅ Audita inserciones de categorías
- ✅ Registra cambios automáticamente

**Registrados en:** `app/Providers/AppServiceProvider.php`

---

## 🚀 EJEMPLOS DE USO PRÁCTICO

### **1. Registrar entrada de inventario desde CompraController**

```php
use App\Services\InventarioService;

class CompraController extends Controller
{
    protected $inventarioService;
    
    public function __construct(InventarioService $inventarioService)
    {
        $this->inventarioService = $inventarioService;
    }
    
    public function recibir(Compra $compra)
    {
        foreach($compra->detalles as $detalle) {
            // Usar procedure para actualizar stock
            $this->inventarioService->actualizarStock(
                productoId: $detalle->producto_id,
                cantidad: $detalle->cantidad,
                tipo: 'entrada',
                razon: "Compra #{$compra->id}",
                userId: auth()->id()
            );
        }
    }
}
```

### **2. Validar stock antes de venta**

```php
use App\Services\InventarioService;

class VentasController extends Controller
{
    protected $inventarioService;
    
    public function store(Request $request)
    {
        $inventarioService = app(InventarioService::class);
        
        foreach($request->productos as $item) {
            // Validar stock disponible
            if(!$inventarioService->validarMovimiento(
                $item['id'], 
                $item['cantidad'], 
                'salida'
            )) {
                return back()->withErrors('Stock insuficiente');
            }
            
            // Registrar salida
            $inventarioService->actualizarStock(
                productoId: $item['id'],
                cantidad: $item['cantidad'],
                tipo: 'salida',
                razon: "Venta",
                userId: auth()->id()
            );
        }
    }
}
```

### **3. Dashboard con estadísticas**

```php
use App\Services\InventarioService;

class DashboardController extends Controller
{
    public function index(InventarioService $inventarioService)
    {
        return view('admin.dashboard', [
            'valor_total' => $inventarioService->valorInventarioTotal(),
            'productos_bajo_stock' => $inventarioService->obtenerProductosBajoStock(),
            'inventario_por_categoria' => $inventarioService->valorInventarioPorCategoria(),
        ]);
    }
}
```

### **4. Reporte de auditoría**

```php
use App\Services\Auditoria\AuditoriaService;

class AuditorController extends Controller
{
    public function reporte(Request $request)
    {
        $reporte = AuditoriaService::generarReporte(
            $request->fecha_inicio,
            $request->fecha_fin
        );
        
        $criticos = AuditoriaService::obtenerCambiosCriticos();
        
        return view('auditor.reporte', compact('reporte', 'criticos'));
    }
    
    public function historial($entidad, $id)
    {
        $historial = AuditoriaService::obtenerHistorialCambios($entidad, $id);
        
        return response()->json($historial);
    }
}
```

---

## 🧪 PRUEBAS DESDE HTTP CLIENT

### **Probar movimiento de stock:**
```http
POST http://localhost:8000/admin/productos/1/movimiento-stock
Content-Type: application/json

{
    "cantidad": 100,
    "tipo": "entrada",
    "razon": "Inventario inicial"
}
```

### **Obtener análisis:**
```http
GET http://localhost:8000/admin/productos/1/analisis
```

### **Reporte general:**
```http
GET http://localhost:8000/admin/productos/reporte/general
```

---

## 📋 TABLA LOG DE MOVIMIENTOS

Nueva tabla creada: `log_movimiento_inventario`

Campos:
- `producto_id` - ID del producto
- `tipo_movimiento` - 'entrada' o 'salida'
- `cantidad` - Cantidad del movimiento
- `razon` - Descripción del movimiento
- `user_id` - Usuario que realizó el movimiento
- `created_at` / `updated_at`

---

## ⚡ TRIGGERS QUE SE EJECUTAN AUTOMÁTICAMENTE

### **Cuando insertas un producto:**
1. Se registra en `auditoria_datos` automáticamente
2. Se crea log en `log_sistema`

### **Cuando actualizas precio:**
1. Trigger audita el cambio en `auditoria_datos`
2. Log de sistema si es cambio significativo

### **Cuando actualizas stock:**
1. Trigger valida que no sea negativo
2. Audita el cambio
3. Si baja del mínimo, crea alerta en `log_sistema`

### **Cuando insertas proveedor o categoría:**
1. Se audita automáticamente
2. Log de sistema registra la operación

---

## 🎨 VENTAJAS DE LA IMPLEMENTACIÓN

✅ **Auditoría automática** - Todos los cambios se registran sin código adicional
✅ **Validaciones en DB** - Stock negativo imposible por trigger
✅ **Alertas automáticas** - Stock bajo mínimo genera log
✅ **Trazabilidad completa** - Cada movimiento queda registrado
✅ **Performance** - Funciones en PL/pgSQL más rápidas
✅ **Integridad** - Transacciones atómicas en procedures
✅ **Reportes eficientes** - Agregaciones en base de datos
✅ **Histórico completo** - Procedure para ver cambios de cualquier recurso

---

## 🔄 PRÓXIMOS PASOS SUGERIDOS

1. **Agregar comando Artisan para limpieza de logs:**
```php
php artisan auditorias:limpiar --dias=90
```

2. **Crear vista de historial en frontend:**
- Historial de cambios por producto
- Timeline de movimientos de inventario

3. **Dashboard de auditoría:**
- Cambios críticos del día
- Usuarios más activos
- Gráficos de movimientos

4. **Notificaciones:**
- Email cuando producto bajo stock mínimo
- Alerta de cambios críticos

5. **API REST para reportes:**
- Endpoint para exportar auditorías
- Reportes programados

---

## 📞 CONSULTAS SQL ÚTILES

### Ver todos los triggers:
```sql
SELECT trigger_name, event_object_table 
FROM information_schema.triggers 
WHERE trigger_schema = 'public';
```

### Ver todas las funciones:
```sql
SELECT routine_name, routine_type 
FROM information_schema.routines 
WHERE routine_schema = 'public';
```

### Probar función:
```sql
SELECT fn_stock_disponible(1);
SELECT * FROM sp_valor_inventario_por_categoria();
```

### Ver auditorías recientes:
```sql
SELECT * FROM auditoria_datos 
ORDER BY timestamp DESC 
LIMIT 20;
```

### Ver movimientos de inventario:
```sql
SELECT p.nombre, l.* 
FROM log_movimiento_inventario l
JOIN productos p ON l.producto_id = p.id
ORDER BY l.created_at DESC;
```

---

## ✅ CONCLUSIÓN

**TODO ESTÁ IMPLEMENTADO Y FUNCIONANDO:**
- ✅ Migraciones ejecutadas
- ✅ Funciones y procedures creados en PostgreSQL
- ✅ Triggers activos automáticamente
- ✅ Servicios listos para usar
- ✅ Observers registrados
- ✅ Controladores con endpoints
- ✅ Rutas configuradas

**La aplicación está lista para usar estas funcionalidades de inmediato.**
