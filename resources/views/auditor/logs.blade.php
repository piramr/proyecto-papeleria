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
        <div class="console-container" id="logs-operaciones"></div>
      </div>
      <div class="tab-pane fade" id="tab-sistema">
        <div class="console-container" id="logs-sistema"></div>
      </div>
      <div class="tab-pane fade" id="tab-login">
        <div class="console-container" id="logs-login"></div>
      </div>
      <div class="tab-pane fade p-5" id="tab-analisis">
        <div class="row mb-4 justify-content-center">
          <div class="col-md-3">
            <label class="small font-weight-bold text-muted text-uppercase">Fecha
              Inicio</label>
            <input type="date" class="form-control form-control-sm" id="likert-fecha-inicio">
          </div>
          <div class="col-md-3">
            <label class="small font-weight-bold text-muted text-uppercase">Fecha
              Fin</label>
            <input type="date" class="form-control form-control-sm" id="likert-fecha-fin">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary btn-sm btn-block" id="btn-filtrar-likert"><i
                class="fas fa-sync-alt mr-2"></i>Filtrar</button>
          </div>
        </div>
        <div class="likert-scale-container">
          <h6 class="text-center font-weight-bold text-muted mb-4 text-uppercase">
            Índice de Salud del Sistema (Likert)</h6>
          <div style="position: relative; height: 120px; min-height: 120px;">
            <canvas id="likertChart" height="100"></canvas>
          </div>
          <!-- Estadísticas dinámicas -->
          <div id="likert-stats" class="mt-3 p-2 bg-light rounded border">
            <div class="text-center text-muted">
              <i class="fas fa-spinner fa-spin mr-1"></i> Cargando estadísticas...
            </div>
          </div>
          <div class="likert-faces mt-3">
            <div class="face-item c-1"><i
                class="fas fa-face-tired face-icon"></i><span
                class="face-label" id="face-critico">1. Crítico</span></div>
            <div class="face-item c-2"><i
                class="fas fa-face-frown face-icon"></i><span
                class="face-label" id="face-riesgo">2. Riesgo</span></div>
            <div class="face-item c-3"><i
                class="fas fa-face-meh face-icon"></i><span 
                class="face-label" id="face-neutral">3. Neutral</span></div>
            <div class="face-item c-4"><i
                class="fas fa-face-smile face-icon"></i><span
                class="face-label" id="face-estable">4. Estable</span></div>
            <div class="face-item c-5"><i
                class="fas fa-face-laugh-beam face-icon"></i><span
                class="face-label" id="face-optimo">5. Óptimo</span></div>
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
    // --- LOGS EN TIEMPO REAL ---
    function renderOperacionRow(log) {
      let tag = '';
      let tagClass = '';
      // Determinar tag según tipo_operacion y resultado
      if (log.resultado === 'OK' || log.resultado === 'exitoso') {
        if (log.tipo_operacion === 'crear' || log.tipo_operacion === 'create') {
          tag = 'CREATE'; tagClass = 'tag-op-create';
        } else if (log.tipo_operacion === 'actualizar' || log.tipo_operacion === 'update') {
          tag = 'UPDATE'; tagClass = 'tag-op-update';
        } else if (log.tipo_operacion === 'eliminar' || log.tipo_operacion === 'delete') {
          tag = 'DELETE'; tagClass = 'tag-op-error';
        } else {
          tag = log.tipo_operacion ? log.tipo_operacion.toUpperCase() : 'OK'; tagClass = 'tag-op-create';
        }
      } else {
        tag = 'ERROR'; tagClass = 'tag-op-error';
      }
      // Construir mensaje descriptivo en español
      let operacionTexto = '';
      switch(log.tipo_operacion) {
        case 'crear': case 'create': operacionTexto = 'creó'; break;
        case 'actualizar': case 'update': operacionTexto = 'actualizó'; break;
        case 'eliminar': case 'delete': operacionTexto = 'eliminó'; break;
        case 'verificar_2fa': operacionTexto = 'verificó 2FA'; break;
        case 'reenviar_2fa': operacionTexto = 'reenvió código 2FA'; break;
        case 'generar_reporte': operacionTexto = 'generó reporte'; break;
        case 'desbloquear': operacionTexto = 'desbloqueó'; break;
        default: operacionTexto = log.tipo_operacion || 'realizó operación';
      }
      let msg = `<b>Usuario responsable #${log.user_id || '?'}</b> ${operacionTexto}`;
      if (log.entidad) msg += ` en <code>${log.entidad}</code>`;
      if (log.recurso_id) msg += ` (Recurso ID: ${log.recurso_id})`;
      if (log.ip_address) msg += ` | IP: ${log.ip_address}`;
      msg += ` | Resultado: <b>${log.resultado || '?'}</b>`;
      if (log.mensaje_error) msg += ` | <span class="text-danger">Error: ${log.mensaje_error}</span>`;
      if (log.codigo_error) msg += ` | Código: ${log.codigo_error}`;
      return `<div class="console-row"><span class="timestamp">[${log.timestamp}]</span><span class="tag ${tagClass}">${tag}</span><span>${msg}</span></div>`;
    }
    function renderSistemaRow(log) {
      let tag = '';
      if (log.nivel && log.nivel.nombre === 'INFO') tag = '<span class="tag tag-sys-info">INFO</span>';
      else if (log.nivel && log.nivel.nombre === 'WARNING') tag = '<span class="tag tag-sys-warn">WARNING</span>';
      else tag = '<span class="tag tag-op-error">ERROR</span>';
      return `<div class="console-row"><span class="timestamp">[${log.timestamp}]</span>${tag}<span>${log.mensaje || ''}</span></div>`;
    }
    function renderLoginRow(log) {
      let tag = '';
      if (log.resultado && log.resultado.nombre === 'EXITOSO') tag = '<span class="tag tag-log-ok">EXITOSO</span>';
      else tag = '<span class="tag tag-log-fail">FALLIDO</span>';
      // Mensaje descriptivo
      let msg = '';
      if (log.user_email) msg += `<b>${log.user_email}</b>`;
      if (log.user_id) msg += ` (ID: ${log.user_id})`;
      if (log.host) msg += ` | IP: ${log.host}`;
      if (log.ubicacion) msg += ` | Ubicación: ${log.ubicacion}`;
      if (log.reintento && log.reintento > 1) msg += ` | Reintentos: ${log.reintento}`;
      if (log.dispositivo) msg += ` | Dispositivo: ${log.dispositivo}`;
      if (log.resultado && log.resultado.description) msg += ` | ${log.resultado.description}`;
      return `<div class="console-row"><span class="timestamp">[${log.timestamp}]</span>${tag}<span>${msg}</span></div>`;
    }
    function fetchLogs() {
      fetch('/auditor/api/logs')
        .then(r => r.json())
        .then(data => {
          const ops = data.operaciones.map(renderOperacionRow).join('');
          const sys = data.sistema.map(renderSistemaRow).join('');
          const logins = data.login.map(renderLoginRow).join('');
          document.getElementById('logs-operaciones').innerHTML = ops;
          document.getElementById('logs-sistema').innerHTML = sys;
          document.getElementById('logs-login').innerHTML = logins;
        });
    }
    setInterval(fetchLogs, 3000);
    document.addEventListener('DOMContentLoaded', fetchLogs);

    // --- GRÁFICO LIKERT CON DATOS REALES ---
    var likertChart;
    var likertData = { critico: 0, riesgo: 0, neutral: 0, estable: 0, optimo: 0 };
    
    function fetchLikertData() {
        var fechaInicio = document.getElementById('likert-fecha-inicio').value;
        var fechaFin = document.getElementById('likert-fecha-fin').value;
        var url = "{{ route('auditor.api.likert') }}";
        var params = [];
        if (fechaInicio) params.push('fecha_inicio=' + fechaInicio);
        if (fechaFin) params.push('fecha_fin=' + fechaFin);
        if (params.length > 0) url += '?' + params.join('&');
        
        return fetch(url)
            .then(r => r.json())
            .then(data => {
                likertData = data;
                updateLikertStats(data);
                return data;
            })
            .catch(err => {
                console.error('Error fetching likert data:', err);
                return likertData;
            });
    }
    
    // Inicializar fechas por defecto (últimos 7 días)
    function initLikertDates() {
        var today = new Date();
        var lastWeek = new Date();
        lastWeek.setDate(today.getDate() - 7);
        
        var fechaFinInput = document.getElementById('likert-fecha-fin');
        var fechaInicioInput = document.getElementById('likert-fecha-inicio');
        
        if (fechaFinInput && !fechaFinInput.value) {
            fechaFinInput.value = today.toISOString().split('T')[0];
        }
        if (fechaInicioInput && !fechaInicioInput.value) {
            fechaInicioInput.value = lastWeek.toISOString().split('T')[0];
        }
    }
    
    // Handler del botón filtrar
    document.addEventListener('DOMContentLoaded', function() {
        initLikertDates();
        var btnFiltrar = document.getElementById('btn-filtrar-likert');
        if (btnFiltrar) {
            btnFiltrar.addEventListener('click', function() {
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Filtrando...';
                this.disabled = true;
                initChart();
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Filtrar';
                    this.disabled = false;
                }, 500);
            });
        }
    });
    
    function updateLikertStats(data) {
        // Actualizar las estadísticas mostradas en la barra
        var statsContainer = document.getElementById('likert-stats');
        if (statsContainer) {
            statsContainer.innerHTML = `
                <div class="d-flex justify-content-between flex-wrap">
                    <span class="badge badge-danger mr-2 mb-1">Crítico: ${data.critico}% (${data.critico_count})</span>
                    <span class="badge badge-warning mr-2 mb-1">Riesgo: ${data.riesgo}% (${data.riesgo_count})</span>
                    <span class="badge badge-secondary mr-2 mb-1">Neutral: ${data.neutral}% (${data.neutral_count})</span>
                    <span class="badge badge-info mr-2 mb-1">Estable: ${data.estable}% (${data.estable_count})</span>
                    <span class="badge badge-success mr-2 mb-1">Óptimo: ${data.optimo}% (${data.optimo_count})</span>
                    <span class="badge badge-dark ml-auto">Total eventos: ${data.total}</span>
                </div>
            `;
        }
        
        // Actualizar las caras con porcentajes
        var faceCritico = document.getElementById('face-critico');
        var faceRiesgo = document.getElementById('face-riesgo');
        var faceNeutral = document.getElementById('face-neutral');
        var faceEstable = document.getElementById('face-estable');
        var faceOptimo = document.getElementById('face-optimo');
        
        if (faceCritico) faceCritico.innerHTML = '1. Crítico<br>(' + data.critico + '%)';
        if (faceRiesgo) faceRiesgo.innerHTML = '2. Riesgo<br>(' + data.riesgo + '%)';
        if (faceNeutral) faceNeutral.innerHTML = '3. Neutral<br>(' + data.neutral + '%)';
        if (faceEstable) faceEstable.innerHTML = '4. Estable<br>(' + data.estable + '%)';
        if (faceOptimo) faceOptimo.innerHTML = '5. Óptimo<br>(' + data.optimo + '%)';
    }
    
    function initChart() {
        var canvas = document.getElementById('likertChart');
        if (!canvas) return;
        
        fetchLikertData().then(data => {
            var ctxL = canvas.getContext('2d');
            if (likertChart) {
                likertChart.destroy();
            }
            likertChart = new Chart(ctxL, {
                type: 'horizontalBar',
                data: {
                    labels: ['Salud Global'],
                    datasets: [
                        { data: [data.critico], backgroundColor: '#dc3545', label: '1. Crítico' },
                        { data: [data.riesgo], backgroundColor: '#fd7e14', label: '2. Riesgo' },
                        { data: [data.neutral], backgroundColor: '#adb5bd', label: '3. Neutral' },
                        { data: [data.estable], backgroundColor: '#0dcaf0', label: '4. Estable' },
                        { data: [data.optimo], backgroundColor: '#198754', label: '5. Óptimo' }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: { display: false },
                    scales: {
                        xAxes: [{ stacked: true, display: false, max: 100 }],
                        yAxes: [{ stacked: true, display: false }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(t, d) {
                                var ds = d.datasets[t.datasetIndex];
                                var count = 0;
                                switch(t.datasetIndex) {
                                    case 0: count = likertData.critico_count; break;
                                    case 1: count = likertData.riesgo_count; break;
                                    case 2: count = likertData.neutral_count; break;
                                    case 3: count = likertData.estable_count; break;
                                    case 4: count = likertData.optimo_count; break;
                                }
                                return ds.label + ": " + ds.data[0] + "% (" + count + " eventos)";
                            }
                        }
                    }
                }
            });
        });
    }
    
    // Actualizar Likert cada 30 segundos
    setInterval(function() {
        var tab = document.querySelector('#tab-analisis');
        if (tab && tab.classList.contains('active')) {
            initChart();
        }
    }, 30000);
    
    $(document).ready(function() { setTimeout(initChart, 200); });
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
      if (e.target.hash === '#tab-analisis') { initChart(); }
    });
  </script>
@endpush
