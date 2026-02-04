<div class="space-y-2">
    @foreach($logs as $log)
        <div class="flex items-start text-xs sm:text-sm p-2 hover:bg-gray-800 rounded transition duration-150 border-l-2 border-yellow-500">
            <div class="w-40 text-gray-500 flex-shrink-0">{{ $log->timestamp->format('Y-m-d H:i:s') }}</div>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <span class="text-yellow-400 font-bold">{{ $log->accion }}</span>
                    <span class="text-gray-300">on</span>
                    <span class="text-white font-mono">{{ $log->tabla }}</span>
                    <span class="text-gray-500">#{{ $log->fila_id }}</span>
                </div>
                 @if($log->accion == 'UPDATE')
                    <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
                        <div class="bg-red-900/20 p-1 rounded text-red-300">
                            - {{ \Illuminate\Support\Str::limit($log->valor_anterior, 50) }}
                        </div>
                        <div class="bg-green-900/20 p-1 rounded text-green-300">
                            + {{ \Illuminate\Support\Str::limit($log->valor_nuevo, 50) }}
                        </div>
                    </div>
                @endif
                <div class="text-xs text-gray-500 mt-1">User: {{ $log->user->nombres ?? 'System' }}</div>
            </div>
        </div>
    @endforeach
    @if($logs->isEmpty())
        <div class="text-gray-500 text-center py-4">-- No data changes --</div>
    @endif
</div>
