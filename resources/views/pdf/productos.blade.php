<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte de Inventario - Papelería</title>
  <style>
    @page {
      size: A4 landscape;
      margin: 10mm;
    }

    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 10px;
      color: #222;
      margin: 10px 0;
      background: #fff;
    }

    .header {
      width: 100%;
      border-bottom: 2px solid #000;
      padding-bottom: 8px;
      margin-bottom: 18px;
    }

    .header table {
      width: 100%;
      border: none;
    }

    .title {
      font-size: 17px;
      font-weight: bold;
      text-transform: uppercase;
    }

    .info-derecha {
      text-align: right;
    }

    .tabla-productos {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
    }

    .tabla-productos th {
      background: #f0f0f0;
      border: 1px solid #bbb;
      padding: 6px 5px;
      text-align: left;
      font-size: 10px;
      font-weight: bold;
    }

    .tabla-productos td {
      border: 1px solid #bbb;
      padding: 6px 5px;
      font-size: 10px;
      vertical-align: top;
    }

    .fila-cebra {
      background: #f7f7f7;
    }

    .total {
      font-weight: bold;
      text-align: right;
      background: #f0f0f0;
    }

    /* Estilos para listas con viñetas */
    ul {
      margin: 0;
      padding-left: 20px;
    }

    ul li {
      margin: 2px 0;
      list-style-type: disc;
    }

    /* Footer */
    .footer {
      width: 100%;
      border-top: 1px solid #bbb;
      padding-top: 8px;
      position: fixed;
      bottom: 18px;
      left: 18px;
      right: 18px;
      font-size: 9px;
      color: #666;
    }
  </style>
</head>

<body>

  <div class="header">
    <table>
      <tr>
        <td class="title">Papeleria Xpress</td>
        <td class="info-derecha">
          <strong>Reporte de registros de productos en inventario</strong><br>
          <strong>Fecha y hora: </strong>{{ now() }}<br>
        </td>
      </tr>
    </table>
  </div>

  <div class="content">
    <table class="tabla-productos" cellspacing="0" cellpadding="0" border="0">
      <thead>
        <tr>
          <th style="width: 2%;">Nro</th>
          <th style="width: 8%;">Código</th>
          <th style="width: 12%;">Nombre</th>
          <th style="width: 16%;">Descripción del producto</th>
          <th style="width: 8%;">Categoría</th>
          <th style="width: 5%;">Stock</th>
          <th style="width: 8%;">Precio de venta</th>
          <th style="width: 12%;">Proveedor/es</th>
          <th style="width: 5%;">Total vendidos</th>
          <th style="width: 8%;">Fecha de registro</th>
        </tr>
      </thead>
      <tbody>
        @php $i = 1; @endphp
        @foreach ($productos as $producto)
          <tr class="{{ $i % 2 == 0 ? 'fila-cebra' : '' }}">
            <td>{{ $i++ }}</td>
            <td>{{ $producto->codigo_barras }}</td>
            <td>{{ $producto->nombre }}</td>
            <td>{{ $producto->caracteristicas }}</td>
            <td>
              {{ $producto->categoria && isset($producto->categoria->nombre) ? $producto->categoria->nombre : '-' }}
            </td>
            <td style="text-align: right;">{{ $producto->cantidad_stock }}</td>
            <td style="text-align: right;">
              ${{ number_format($producto->precio_unitario, 2) }}</td>
            <td>
              @if (isset($producto->proveedores) &&
                      ((is_object($producto->proveedores) &&
                          method_exists($producto->proveedores, 'count') &&
                          $producto->proveedores->count()) ||
                          (is_array($producto->proveedores) &&
                              count($producto->proveedores))))
                @if ((is_object($producto->proveedores) && $producto->proveedores->count() > 1) || 
                     (is_array($producto->proveedores) && count($producto->proveedores) > 1))
                  <ul>
                    @if (is_object($producto->proveedores) && method_exists($producto->proveedores, 'pluck'))
                      @foreach ($producto->proveedores as $proveedor)
                        <li>{{ $proveedor->nombre }}</li>
                      @endforeach
                    @elseif (is_array($producto->proveedores))
                      @foreach ($producto->proveedores as $p)
                        <li>{{ $p['nombre'] }}</li>
                      @endforeach
                    @endif
                  </ul>
                @else
                  @if (is_object($producto->proveedores) && method_exists($producto->proveedores, 'pluck'))
                    {{ $producto->proveedores->pluck('nombre')->implode(', ') }}
                  @elseif (is_array($producto->proveedores))
                    {{ implode(', ', array_map(function ($p) { return $p['nombre']; }, $producto->proveedores)) }}
                  @endif
                @endif
              @else
                <span style="color: #888;">Sin proveedores</span>
              @endif
            </td>
            <td style="text-align: right;">
              {{ $producto->total_vendidos ?? 0 }}
            </td>
            <td>
              {{ $producto->created_at ? (is_string($producto->created_at) ? \Carbon\Carbon::parse($producto->created_at)->format('d/m/Y H:i:s') : $producto->created_at->format('d/m/Y H:i:s')) : '-' }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="footer">
    <table style="width: 100%;">
      <tr>
        <td>Documento generado automáticamente por el sistema de inventarios.
        </td>
      </tr>
    </table>
  </div>

</body>

</html>
