<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte de Proveedores - Papelería Xpress</title>
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

    .tabla-proveedores {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
    }

    .tabla-proveedores th {
      background: #f0f0f0;
      border: 1px solid #bbb;
      padding: 6px 5px;
      text-align: left;
      font-size: 10px;
      font-weight: bold;
    }

    .tabla-proveedores td {
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
          <strong>Reporte de Proveedores</strong><br>
          <strong>Fecha y hora: </strong>{{ now() }}<br>
        </td>
      </tr>
    </table>
  </div>

  <div class="content">
    <table class="tabla-proveedores" cellspacing="0" cellpadding="0" border="0">
      <thead>
        <tr>
          <th style="width: 2%;">Nro</th>
          <th style="width: 7%;">RUC</th>
          <th style="width: 10%;">Razón Social</th>
          <th style="width: 10%;">Email</th>
          <th style="width: 8%;">Tel. Principal</th>
          <th style="width: 8%;">Tel. Secundario</th>
          <th style="width: 13%;">Direcciones</th>
          <th style="width: 12%;">Productos Ofrecidos</th>
          <th style="width: 6%;">Nro. Compras</th>
          <th style="width: 7%;">Gastos</th>
          <th style="width: 8%;">Fecha Registro</th>
        </tr>
      </thead>
      <tbody>
        @php $i = 1; @endphp
        @foreach ($proveedores as $proveedor)
          <tr class="{{ $i % 2 == 0 ? 'fila-cebra' : '' }}">
            <td style="text-align: center;">{{ $i++ }}</td>
            <td>{{ $proveedor->ruc }}</td>
            <td>{{ $proveedor->nombre }}</td>
            <td>{{ $proveedor->email }}</td>
            <td>{{ $proveedor->telefono_principal }}</td>
            <td>{{ $proveedor->telefono_secundario ?? '-' }}</td>
            <td>
              @if ($proveedor->direcciones->count())
                @if ($proveedor->direcciones->count() > 1)
                  <ul>
                    @foreach ($proveedor->direcciones as $dir)
                      <li>{{ $dir->calle }}, {{ $dir->ciudad }}, {{ $dir->provincia }}</li>
                    @endforeach
                  </ul>
                @else
                  @foreach ($proveedor->direcciones as $dir)
                    {{ $dir->calle }}, {{ $dir->ciudad }}, {{ $dir->provincia }}
                  @endforeach
                @endif
              @else
                <span style="color: #888;">-</span>
              @endif
            </td>
            <td>
              @if ($proveedor->productos->count())
                @if ($proveedor->productos->count() > 1)
                  <ul>
                    @foreach ($proveedor->productos as $producto)
                      <li>{{ $producto->nombre }}</li>
                    @endforeach
                  </ul>
                @else
                  @foreach ($proveedor->productos as $producto)
                    {{ $producto->nombre }}
                  @endforeach
                @endif
              @else
                <span style="color: #888;">-</span>
              @endif
            </td>
            <td style="text-align: right;">{{ $proveedor->nro_compras ?? 0 }}</td>
            <td style="text-align: right;">
              ${{ number_format($proveedor->total_gastos ?? 0, 2) }}
            </td>
            <td>
              {{ $proveedor->created_at ? (is_string($proveedor->created_at) ? \Carbon\Carbon::parse($proveedor->created_at)->format('d/m/Y H:i:s') : $proveedor->created_at->format('d/m/Y H:i:s')) : '-' }}
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
