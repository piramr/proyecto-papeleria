<div class="space-y-2">
    @foreach($logs as $log)
        <div class="flex items-start text-xs sm:text-sm p-2 hover:bg-gray-800 rounded transition duration-150 border-l-2 border-red-500">
            <div class="w-40 text-gray-500 flex-shrink-0">{{ $log->attempt_fecha->format('Y-m-d H:i:s') }}</div>
            <div class="flex-1 text-gray-300">
                Failed login for user: <span class="text-red-400 font-bold">{{ $log->username_attempted }}</span>
                from IP <span class="text-gray-400">{{ $log->host }}</span>
                <div class="text-xs text-red-500 mt-1">Reason: {{ $log->failure_reason }}</div>
            </div>
        </div>
    @endforeach
    @if($logs->isEmpty())
        <div class="text-gray-500 text-center py-4">-- No failed attempts --</div>
    @endif
</div>
