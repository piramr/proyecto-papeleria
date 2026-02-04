<div wire:poll.2s>
    <!-- Tabs -->
    <div class="mb-3 border-b border-gray-700">
        <nav class="-mb-px flex space-x-2 sm:space-x-8 overflow-x-auto" aria-label="Tabs">
            <button wire:click="setTab('resources')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                    {{ $activeTab === 'resources' ? 'border-indigo-500 text-indigo-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-network-wired mr-2"></i> Tráfico / API
            </button>
            <button wire:click="setTab('logins')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                    {{ $activeTab === 'logins' ? 'border-green-500 text-green-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-sign-in-alt mr-2"></i> Inicios de Sesión
            </button>
            <button wire:click="setTab('attempts')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                    {{ $activeTab === 'attempts' ? 'border-red-500 text-red-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-exclamation-triangle mr-2"></i> Intentos Fallidos
            </button>
            <button wire:click="setTab('dml')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                    {{ $activeTab === 'dml' ? 'border-yellow-500 text-yellow-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-database mr-2"></i> Control de Cambios (DML)
            </button>
            <button wire:click="setTab('ddl')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                    {{ $activeTab === 'ddl' ? 'border-purple-500 text-purple-500' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-table mr-2"></i> Estructura (DDL)
            </button>
        </nav>
    </div>

    <!-- Live Indicator -->
    <div class="flex justify-end mb-2">
         <span class="flex items-center space-x-2 bg-gray-900 border border-gray-700 rounded-full px-3 py-1">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <span class="text-xs text-gray-400 font-mono">MONITOREO EN VIVO</span>
        </span>
    </div>

    <!-- Console Container -->
    <div class="audit-console-container bg-gray-900 border border-gray-700 p-4 rounded text-sm shadow-xl">
        <div class="audit-console" style="max-height: 600px; overflow-y: auto;">
            
            @if($logs->isEmpty())
                <div class="text-gray-500 text-center py-10 font-mono">-- No hay registros recientes --</div>
            @endif

            @foreach($logs as $log)
                <div class="mb-3 border-l-2 pl-3 py-1 hover:bg-gray-800/50 transition duration-150
                    {{ $activeTab === 'attempts' ? 'border-red-500' : 'border-gray-600' }}">
                    
                    <!-- Timestamp & User -->
                    <div class="flex justify-between text-xs text-gray-500 font-mono mb-1">
                        <span>
                            <span class="text-blue-400">[{{ $activeTab === 'logins' ? $log->login_fecha : ($activeTab === 'attempts' ? $log->attempt_fecha : ($activeTab === 'ddl' ? $log->ddl_fecha : $log->timestamp)) }}]</span>
                        </span>
                        <span>
                            @if(isset($log->user))
                                <i class="fas fa-user text-gray-600"></i> {{ $log->user->nombre_usuario ?? ($log->user->nombres ?? 'Desconocido') }}
                            @elseif(isset($log->username))
                                <span class="text-red-400">{{ $log->username }}</span>
                            @else
                                System
                            @endif
                        </span>
                    </div>

                    <!-- Content Body Based on Tab -->
                    @if($activeTab === 'resources')
                        <div class="flex items-start gap-2">
                            <span class="px-2 py-0.5 rounded text-xs font-bold w-16 text-center
                                {{ $log->http_method == 'GET' ? 'bg-blue-900 text-blue-200' : '' }}
                                {{ $log->http_method == 'POST' ? 'bg-green-900 text-green-200' : '' }}
                                {{ $log->http_method == 'PUT' ? 'bg-yellow-900 text-yellow-200' : '' }}
                                {{ $log->http_method == 'DELETE' ? 'bg-red-900 text-red-200' : '' }}
                            ">
                                {{ $log->http_method }}
                            </span>
                            <span class="text-gray-300 font-mono break-all">{{ $log->endpoint }}</span>
                            <span class="ml-auto text-xs font-mono
                                {{ $log->response_code >= 200 && $log->response_code < 300 ? 'text-green-400' : 'text-red-400' }}
                            ">
                                {{ $log->response_code }}
                            </span>
                        </div>
                    @elseif($activeTab === 'logins')
                         <div class="flex items-center gap-2">
                            <span class="text-green-400 font-bold"><i class="fas fa-check-circle"></i> EXITO</span>
                            <span class="text-gray-400">IP: <span class="text-gray-200">{{ $log->host }}</span></span>
                            @if($log->logout_fecha)
                                <span class="text-gray-500 text-xs">(Sesión finalizada: {{ \Carbon\Carbon::parse($log->duration_seconds)->format('H:i:s') }} duración)</span>
                            @else
                                <span class="text-green-500 text-xs animate-pulse">● Activo</span>
                            @endif
                        </div>
                    @elseif($activeTab === 'attempts')
                        <div class="flex flex-col">
                            <div class="text-red-400 font-bold mb-1">
                                <i class="fas fa-times-circle"></i> FALLIDO
                            </div>
                            <div class="text-gray-300">
                                <span class="text-gray-500">Motivo:</span> {{ $log->failure_reason }}
                            </div>
                            <div class="text-gray-500 text-xs mt-1">IP: {{ $log->ip_address }} | Navegador: {{ \Illuminate\Support\Str::limit($log->user_agent, 30) }}</div>
                        </div>
                    @elseif($activeTab === 'dml')
                         <div class="font-mono text-sm">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded text-xs font-bold
                                    {{ $log->accion == 'INSERT' ? 'bg-green-900 text-green-200' : '' }}
                                    {{ $log->accion == 'UPDATE' ? 'bg-yellow-900 text-yellow-200' : '' }}
                                    {{ $log->accion == 'DELETE' ? 'bg-red-900 text-red-200' : '' }}
                                    {{ $log->accion == 'RESTORE' ? 'bg-blue-900 text-blue-200' : '' }}
                                ">
                                    {{ $log->accion == 'INSERT' ? 'CREAR' : ($log->accion == 'UPDATE' ? 'ACTUALIZAR' : ($log->accion == 'DELETE' ? 'ELIMINAR' : $log->accion)) }}
                                </span>
                                <span class="text-gray-300">{{ $log->tabla }}</span>
                                <span class="text-gray-500 text-xs">#{{ $log->fila_id }}</span>
                            </div>
                            
                            @if($log->accion == 'UPDATE')
                                <div class="grid grid-cols-2 gap-2 mt-2 text-xs bg-black/30 p-2 rounded">
                                    <div>
                                        <div class="text-red-400 mb-1 border-b border-red-900/50">ANTES</div>
                                        @foreach(json_decode($log->valor_anterior, true) ?? [] as $key => $val)
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">{{ $key }}:</span>
                                                <span class="text-red-300">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div>
                                        <div class="text-green-400 mb-1 border-b border-green-900/50">DESPUÉS</div>
                                         @foreach(json_decode($log->valor_nuevo, true) ?? [] as $key => $val)
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">{{ $key }}:</span>
                                                <span class="text-green-300">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @elseif($log->accion == 'INSERT')
                                <div class="mt-2 text-xs bg-black/30 p-2 rounded">
                                    <div class="text-green-400 mb-1">DATOS NUEVOS</div>
                                    @foreach(json_decode($log->valor_nuevo, true) ?? [] as $key => $val)
                                        <div class="inline-block mr-3">
                                            <span class="text-gray-500">{{ $key }}:</span>
                                            <span class="text-green-300">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @elseif($activeTab === 'ddl')
                         <div class="font-mono">
                            <span class="text-purple-400 font-bold">{{ $log->evento }}</span>
                            <span class="text-white">{{ $log->objeto_tipo }}</span>
                            <span class="text-gray-400">{{ $log->objeto_nombre }}</span>
                            <div class="mt-1 p-2 bg-black rounded text-gray-500 text-xs font-mono border border-gray-700">
                                {{ \Illuminate\Support\Str::limit($log->sql_command, 150) }}
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    
    <style>
        .audit-console::-webkit-scrollbar {
            width: 8px;
        }
        .audit-console::-webkit-scrollbar-track {
            background: #0d1117; 
        }
        .audit-console::-webkit-scrollbar-thumb {
            background: #30363d; 
            border-radius: 4px;
        }
        .audit-console::-webkit-scrollbar-thumb:hover {
            background: #58a6ff; 
        }
    </style>
</div>
