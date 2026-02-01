<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura #{{ $factura->numero_factura }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .factura-container {
            max-width: 900px;
            margin: 10px auto;
            background-color: white;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .factura-header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        .factura-header h1 {
            margin: 0;
            font-size: 36px;
            font-weight: bold;
        }
        .empresa-info {
            margin-top: 5px;
            font-size: 11px;
            line-height: 1.3;
        }
        .factura-numero {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 12px;
        }
        .seccion {
            margin-bottom: 10px;
        }
        .seccion-titulo {
            font-weight: bold;
            font-size: 11px;
            color: #666;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .seccion-contenido {
            font-size: 12px;
            line-height: 1.4;
        }
        .tabla-productos {
            width: 100%;
            margin: 8px 0;
            border-collapse: collapse;
        }
        .tabla-productos thead {
            background-color: #f0f0f0;
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
        }
        .tabla-productos th {
            padding: 5px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        .tabla-productos td {
            padding: 4px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        .tabla-productos .numero {
            text-align: right;
        }
        .totales {
            margin: 8px 0;
            float: right;
            width: 250px;
        }
        .totales-tabla {
            width: 100%;
            border-collapse: collapse;
        }
        .totales-tabla tr {
            height: 18px;
        }
        .totales-tabla td:first-child {
            text-align: right;
            padding-right: 15px;
            font-size: 12px;
        }
        .totales-tabla td:last-child {
            text-align: right;
            font-size: 12px;
        }
        .totales-tabla .total {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #333;
            border-bottom: 1px solid #333;
            padding: 5px 0;
        }
        .pie-factura {
            clear: both;
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            line-height: 1.3;
        }
        .footer-text {
            margin-top: 5px;
            font-style: italic;
        }
        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
            }
            .factura-container {
                box-shadow: none;
                margin: 0;
                padding: 10px;
                page-break-after: avoid;
            }
            .no-print {
                display: none !important;
            }
            html, body {
                height: 100%;
            }
        }
            }
        }
        .btn-print {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="no-print btn-print text-center">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Imprimir
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Cerrar
        </button>
    </div>

    <div class="factura-container">
        <!-- Encabezado -->
        <div class="factura-header">
            <h1>FACTURA</h1>
            <div class="empresa-info">
                <h5>Papelería XYZ</h5>
                <p class="mb-1">RUC/Cédula: XXXXXXXXXXXXX</p>
                <p class="mb-1">Dirección: Calle Principal, Local 123</p>
                <p class="mb-0">Teléfono: +58 XXX-XXXX-XX | Email: info@papeleria.com</p>
            </div>
        </div>

        <!-- Número de Factura y Fecha -->
        <div class="factura-numero">
            <div>
                <strong>Factura #:</strong> {{ $factura->numero_factura }}
            </div>
            <div>
                <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($factura->fecha_hora)->format('d/m/Y H:i') }}
            </div>
        </div>

        <!-- Cliente -->
        <div class="row">
            <div class="col-md-6">
                <div class="seccion">
                    <div class="seccion-titulo">Cliente</div>
                    <div class="seccion-contenido">
                        <p class="mb-1"><strong>Cédula/RUC:</strong> {{ $factura->cliente->cedula }}</p>
                        <p class="mb-1"><strong>Nombre:</strong> {{ $factura->cliente->nombres }} {{ $factura->cliente->apellidos }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $factura->cliente->email ?? 'N/A' }}</p>
                        <p class="mb-0"><strong>Teléfono:</strong> {{ $factura->cliente->telefono ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="seccion">
                    <div class="seccion-titulo">Forma de Pago</div>
                    <div class="seccion-contenido">
                        <p class="mb-0"><strong>{{ $factura->tipoPago->nombre }}</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de productos -->
        <table class="tabla-productos">
            <thead>
                <tr>
                    <th style="width: 50%;">Descripción del Producto</th>
                    <th style="width: 15%;" class="numero">Cantidad</th>
                    <th style="width: 17.5%;" class="numero">Precio Unitario</th>
                    <th style="width: 17.5%;" class="numero">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($factura->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td class="numero">{{ $detalle->cantidad }}</td>
                        <td class="numero">${{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="numero">${{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totales -->
        <div class="totales">
            <table class="totales-tabla">
                <tr>
                    <td>Subtotal:</td>
                    <td>${{ number_format($factura->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>IVA (15%):</td>
                    <td>${{ number_format($factura->iva, 2) }}</td>
                </tr>
                <tr class="total">
                    <td>TOTAL:</td>
                    <td>${{ number_format($factura->total, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Pie de página -->
        <div class="pie-factura">
            <p><strong>¡GRACIAS POR SU COMPRA!</strong></p>
            <div class="footer-text">
                <p>Esta factura es válida como comprobante de pago y debe ser conservada por el cliente.</p>
                <p>Para consultas, contacte a nuestro servicio al cliente.</p>
            </div>
        </div>
    </div>
</body>
</html>
