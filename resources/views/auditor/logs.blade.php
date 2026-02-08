@extends('layouts.app')

@section('title', 'Logs del sistema')

@push('styles')

  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background-color: #f4f7f9;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card,
    .nav-link,
    .console-container,
    .btn,
    .form-control,
    .badge {
      border-radius: 0 !important;
    }

    .log-nav {
      border-bottom: 2px solid #dee2e6;
    }

    .log-nav .nav-link {
      color: #6c757d;
      font-weight: 600;
      border: none;
      padding: 15px 25px;
      transition: all 0.2s;
    }

    .log-nav .nav-link.active {
      color: #007bff;
      background: transparent;
      border-bottom: 2px solid #007bff;
      margin-bottom: -2px;
    }

    .console-container {
      background-color: #0f172a;
      color: #cbd5e1;
      font-family: 'Consolas', monospace;
      font-size: 0.85rem;
      height: 500px;
      overflow-y: auto;
      border: 1px solid #1e293b;
    }

    .console-row {
      padding: 10px 20px;
      border-bottom: 1px solid #1e293b;
      display: flex;
      align-items: center;
    }

    .console-row:hover {
      background-color: #1e293b;
    }

    .timestamp {
      color: #64748b;
      width: 175px;
      flex-shrink: 0;
    }

    .tag {
      font-size: 0.75rem;
      font-weight: 800;
      margin-right: 15px;
      min-width: 95px;
      text-align: center;
      padding: 2px 0;
      border: 1px solid transparent;
      text-transform: uppercase;
    }

    .tag-op-create {
      color: #28a745;
      border-bottom: 1px solid #28a745;
    }

    .tag-op-update {
      color: #ffc107;
      border-bottom: 1px solid #ffc107;
    }

    .tag-op-error {
      color: #dc3545;
      border-bottom: 1px solid #dc3545;
    }

    .tag-sys-info {
      color: #17a2b8;
      border-bottom: 1px solid #17a2b8;
    }

    .tag-sys-warn {
      color: #fd7e14;
      border-bottom: 1px solid #fd7e14;
    }

    .tag-log-ok {
      color: #20c997;
      border-bottom: 1px solid #20c997;
    }

    .tag-log-fail {
      color: #e83e8c;
      border-bottom: 1px solid #e83e8c;
    }

    .likert-scale-container {
      background: white;
    }

    .likert-faces {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }

    .face-item {
      text-align: center;
      flex: 1;
    }

    .face-icon {
      font-size: 2rem;
      display: block;
      margin-bottom: 8px;
    }

    .face-label {
      font-size: 0.8rem;
      font-weight: bold;
    }

    .c-1 {
      color: #dc3545;
    }

    .c-2 {
      color: #fd7e14;
    }

    .c-3 {
      color: #adb5bd;
    }

    .c-4 {
      color: #0dcaf0;
    }

    .c-5 {
      color: #198754;
    }

    .criteria-box {
      background: #ffffff;
      border: 1px solid #dee2e6;
      border-top: 3px solid #007bff;
    }

    .table-criteria thead th {
      background: #f8f9fa;
      font-size: 0.75rem;
      color: #495057;
      border-bottom: 2px solid #dee2e6;
    }

    .table-criteria td {
      font-size: 0.8rem;
      vertical-align: middle;
    }

    .db-ref {
      font-family: 'Consolas', monospace;
      color: #e83e8c;
      font-size: 0.75rem;
      background: #f1f3f5;
      padding: 2px 4px;
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <div class="mb-2">
      <h2 class="font-weight-bold">Centro de Logging</h2>
    </div>

    <ul class="nav nav-tabs log-nav" id="logTabSystem" role="tablist">
      <li class="nav-item"><a class="nav-link active" data-toggle="tab"
          href="#tab-operaciones">Operaciones</a></li>
      <li class="nav-item"><a class="nav-link" data-toggle="tab"
          href="#tab-sistema">Sistema</a></li>
      <li class="nav-item"><a class="nav-link" data-toggle="tab"
          href="#tab-login">Login</a></li>
      <li class="nav-item"><a class="nav-link" data-toggle="tab"
          href="#tab-analisis">Análisis</a></li>
    </ul>

    <div class="tab-content border-top-0 border bg-white shadow-sm">
      <div class="tab-pane fade show active" id="tab-operaciones">
        <div class="console-container">
          <div class="console-row">
            <span class="timestamp">[2026-02-08 00:10:05]</span>
            <span class="tag tag-op-update">UPDATE</span>
            <span>User_10: Entity 'Proveedor' (ID: 45) updated successfully.
              Result: OK</span>
          </div>
          <div class="console-row">
            <span class="timestamp">[2026-02-08 00:10:45]</span>
            <span class="tag tag-op-error">ERROR</span>
            <span>User_05: DELETE failed on 'Producto' (ID: 12). Code: 500 |
              Internal Error</span>
          </div>
          <script>
            for (let i = 0; i < 15; i++) document.write(
              '<div class="console-row"><span class="timestamp">[2026-02-08 00:12:00]</span><span class="tag tag-op-create">CREATE</span><span>User_01: New record in Entity \'Categoria\' | Result: OK</span></div>'
              );
          </script>
        </div>
      </div>
      <div class="tab-pane fade" id="tab-sistema">
        <div class="console-container">
          <div class="console-row">
            <span class="timestamp">[2026-02-08 00:00:01]</span>
            <span class="tag tag-sys-info">INFO</span>
            <span>LOG_SISTEMA: Código de doble factor enviado a user_id:
              102.</span>
          </div>
          <div class="console-row">
            <span class="timestamp">[2026-02-08 00:05:22]</span>
            <span class="tag tag-sys-warn">WARNING</span>
            <span>Ajuste de sistema: IVA actualizado a 15% por
              Administrador.</span>
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="tab-login">
        <div class="console-container">
          <div class="console-row">
            <span class="timestamp">[2026-02-08 00:08:12]</span>
            <span class="tag tag-log-ok">EXITOSO</span>
            <span>admin@paint.co | IP: 186.42.10.5 | Localizacion: Quito,
              Ecuador</span>
          </div>
          <div class="console-row">
            <span class="timestamp">[2026-02-08 00:09:45]</span>
            <span class="tag tag-log-fail">FALLIDO</span>
            <span>test@user.com | Reintento: 3/3 | Resultado:
              USUARIO_BLOQUEADO</span>
          </div>
        </div>
      </div>
      <div class="tab-pane fade p-5" id="tab-analisis">
        <div class="row mb-4 justify-content-center">
          <div class="col-md-3">
            <label class="small font-weight-bold text-muted text-uppercase">Fecha
              Inicio</label>
            <input type="date" class="form-control form-control-sm"
              value="2026-02-01">
          </div>
          <div class="col-md-3">
            <label class="small font-weight-bold text-muted text-uppercase">Fecha
              Fin</label>
            <input type="date" class="form-control form-control-sm"
              value="2026-02-08">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary btn-sm btn-block"><i
                class="fas fa-sync-alt mr-2"></i>Filtrar</button>
          </div>
        </div>
        <div class="likert-scale-container">
          <h6 class="text-center font-weight-bold text-muted mb-4 text-uppercase">
            Índice de Salud del Sistema (Likert)</h6>
          <div style="position: relative; height: 120px; min-height: 120px;">
            <canvas id="likertChart" height="100"></canvas>
          </div>
          <div class="likert-faces">
            <div class="face-item c-1"><i
                class="fas fa-face-tired face-icon"></i><span
                class="face-label">1. Crítico<br>(8%)</span></div>
            <div class="face-item c-2"><i
                class="fas fa-face-frown face-icon"></i><span
                class="face-label">2. Riesgo<br>(12%)</span></div>
            <div class="face-item c-3"><i
                class="fas fa-face-meh face-icon"></i><span class="face-label">3.
                Neutral<br>(15%)</span></div>
            <div class="face-item c-4"><i
                class="fas fa-face-smile face-icon"></i><span
                class="face-label">4. Estable<br>(35%)</span></div>
            <div class="face-item c-5"><i
                class="fas fa-face-laugh-beam face-icon"></i><span
                class="face-label">5. Óptimo<br>(30%)</span></div>
          </div>
          <div class="criteria-box mt-5 shadow-sm">
            <div class="p-3 border-bottom bg-light">
              <h6 class="mb-0 font-weight-bold text-dark small text-uppercase"><i
                  class="fas fa-table mr-2"></i>Criterios de Evaluación</h6>
            </div>
            <div class="table-responsive">
              <table class="table table-bordered table-criteria mb-0">
                <thead>
                  <tr>
                    <th width="20%">Puntaje Likert</th>
                    <th width="30%">Origen de Datos (Tablas)</th>
                    <th width="50%">Condición de Valor y Lógica</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="font-weight-bold c-1 text-center">1. Crítico</td>
                    <td><span class="db-ref">LOG_SISTEMA</span><br><span
                        class="db-ref">LOG_LOGIN</span></td>
                    <td>Registros con <span class="db-ref">nivel_log_id</span> =
                      <code>FATAL</code> o intentos agotados con <span
                        class="db-ref">resultado_log_id</span> =
                      <code>USUARIO_BLOQUEADO</code>.</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold c-2 text-center">2. Riesgo</td>
                    <td><span class="db-ref">LOG_OPERACION</span><br><span
                        class="db-ref">LOG_SISTEMA</span></td>
                    <td>Operaciones con <span class="db-ref">resultado</span> =
                      <code>ERROR</code> y logs de sistema con <span
                        class="db-ref">nombre</span> = <code>ERROR</code>.</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold c-3 text-center">3. Neutral</td>
                    <td><span class="db-ref">LOG_LOGIN</span><br><span
                        class="db-ref">LOG_SISTEMA</span></td>
                    <td>Cuando <span class="db-ref">reintento</span> es > 1 en
                      login, o alertas con <span class="db-ref">nombre</span> =
                      <code>WARNING</code> en sistema.</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold c-4 text-center">4. Estable</td>
                    <td><span class="db-ref">LOG_LOGIN</span><br><span
                        class="db-ref">LOG_SISTEMA</span></td>
                    <td>Resultados intermedios como <code>CODIGO_ENVIADO</code> o
                      eventos <span class="db-ref">nombre</span> =
                      <code>INFO</code> de rutina.</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold c-5 text-center">5. Óptimo</td>
                    <td><span class="db-ref">LOG_OPERACION</span><br><span
                        class="db-ref">LOG_LOGIN</span></td>
                    <td>Cierre de transacciones con <span
                        class="db-ref">resultado</span> = <code>OK</code> y
                      accesos <span class="db-ref">nombre</span> =
                      <code>EXITOSO</code>.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4"></script>
  <script>
    var likertChart;

    function initChart() {
      var canvas = document.getElementById('likertChart');
      if (!canvas) return;
      var ctxL = canvas.getContext('2d');
      if (likertChart) {
        likertChart.destroy();
      }
      likertChart = new Chart(ctxL, {
        type: 'horizontalBar',
        data: {
          labels: ['Salud Global'],
          datasets: [{
              data: [8],
              backgroundColor: '#dc3545',
              label: '1. Crítico'
            },
            {
              data: [12],
              backgroundColor: '#fd7e14',
              label: '2. Riesgo'
            },
            {
              data: [15],
              backgroundColor: '#adb5bd',
              label: '3. Neutral'
            },
            {
              data: [35],
              backgroundColor: '#0dcaf0',
              label: '4. Estable'
            },
            {
              data: [30],
              backgroundColor: '#198754',
              label: '5. Óptimo'
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: {
            display: false
          },
          scales: {
            xAxes: [{
              stacked: true,
              display: false
            }],
            yAxes: [{
              stacked: true,
              display: false
            }]
          },
          tooltips: {
            callbacks: {
              label: function(t, d) {
                var ds = d.datasets[t.datasetIndex];
                return ds.label + ": " + ds.data[0] + "%";
              }
            }
          }
        }
      });
    }
    // Inicializar el gráfico si la pestaña ya está activa al cargar la página
    $(document).ready(function() {
      // Forzar inicialización del gráfico al cargar la página
      setTimeout(initChart, 200);
    });
    // Inicializar el gráfico al mostrar la pestaña
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
      if (e.target.hash === '#tab-analisis') {
        initChart();
      }
    });
  </script>
@endpush
