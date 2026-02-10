<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Reporte de Ventas - Papelería Xpress</title>
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

    .tabla-ventas {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 24px;
    }

    .tabla-ventas th {
      background: #f0f0f0;
      border: 1px solid #bbb;
      padding: 6px 5px;
      text-align: left;
      font-size: 10px;
      font-weight: bold;
    }

    .tabla-ventas td {
      border: 1px solid #bbb;
      padding: 6px 5px;
      font-size: 10px;
      vertical-align: top;
    }

    .fila-cebra {
      background: #f7f7f7;
    }

    .total-row {
      font-weight: bold;
      background: #e0e0e0;
    }

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
        <td class="title">Papelería Xpress</td>
        <td class="info-derecha">
          <strong>Reporte de Ventas</strong><br>
          <strong>Fecha y hora: </strong>{{ now()->format('d/m/Y H:i:s') }}<br>
        </td>
      </tr>
    </table>
  </div>

  <div class="content">
    <table class="tabla-ventas" cellspacing="0" cellpadding="0" border="0">
      <thead>
        <tr>
          <th style="width: 3%;">Nro</th>
          <th style="width: 6%;">Factura #</th>
          <th style="width: 15%;">Cliente</th>
          <th style="width: 10%;">Cédula</th>
          <th style="width: 12%;">Fecha</th>
          <th style="width: 10%;">Tipo Pago</th>
          <th style="width: 10%;">Subtotal</th>
          <th style="width: 10%;">Total</th>
          <th style="width: 12%;">Usuario</th>
        </tr>
      </thead>
      <tbody>
        @php $i = 1; @endphp
        @foreach ($facturas as $factura)
          <tr class="{{ $i % 2 == 0 ? 'fila-cebra' : '' }}">
            <td style="text-align: center;">{{ $i++ }}</td>
            <td style="text-align: center;">{{ $factura->id }}</td>
            <td>{{ ($factura->cliente->nombres ?? '') . ' ' . ($factura->cliente->apellidos ?? '') }}</td>
            <td>{{ $factura->cliente->cedula ?? '-' }}</td>
            <td>{{ $factura->fecha_hora ? $factura->fecha_hora->format('d/m/Y H:i') : '-' }}</td>
            <td>{{ $factura->tipoPago->nombre ?? '-' }}</td>
            <td style="text-align: right;">${{ number_format($factura->subtotal, 2) }}</td>
            <td style="text-align: right;">${{ number_format($factura->total, 2) }}</td>
            <td>{{ $factura->usuario->name ?? '-' }}</td>
          </tr>
        @endforeach
        <tr class="total-row">
          <td colspan="6" style="text-align: right; padding-right: 10px;">TOTALES:</td>
          <td style="text-align: right;">${{ number_format($totales->subtotal ?? 0, 2) }}</td>
          <td style="text-align: right;">${{ number_format($totales->total ?? 0, 2) }}</td>
          <td style="text-align: center;">{{ $totales->total_facturas ?? 0 }} ventas</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="footer">
    <table style="width: 100%;">
      <tr>
        <td>Documento generado automáticamente por el sistema de gestión.</td>
      </tr>
    </table>
  </div>

</body>

</html>
