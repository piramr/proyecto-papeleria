<div class="space-y-2">
    @foreach($logs as $log)
        <div class="flex items-start text-xs sm:text-sm p-2 hover:bg-gray-800 rounded transition duration-150 border-l-2 border-purple-500">
            <div class="w-40 text-gray-500 flex-shrink-0">{{ $log->ddl_fecha->format('Y-m-d H:i:s') }}</div>
            <div class="flex-1">
                <div class="flex items-center gap-2">
                    <span class="text-purple-400 font-bold">{{ $log->evento }}</span>
                    <span class="text-gray-300">{{ $log->objeto_tipo }}</span>
                    <span class="text-white font-mono">{{ $log->objeto_nombre }}</span>
                </div>
                <div class="text-gray-500 mt-1 text-xs font-mono bg-gray-900 p-1 rounded border border-gray-700">
                    {{ \Illuminate\Support\Str::limit($log->sql_command, 100) }}
                </div>
                <div class="text-xs text-gray-500 mt-1">User: {{ $log->user->nombres ?? 'System' }}</div>
            </div>
        </div>
    @endforeach
     @if($logs->isEmpty())
        <div class="text-gray-500 text-center py-4">-- No schema changes --</div>
    @endif
</div>
