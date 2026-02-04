<div class="space-y-2">
    @foreach($logs as $log)
        <div class="flex items-start text-xs sm:text-sm p-2 hover:bg-gray-800 rounded transition duration-150 border-l-2 border-green-500">
            <div class="w-40 text-gray-500 flex-shrink-0">{{ $log->login_fecha->format('Y-m-d H:i:s') }}</div>
            <div class="flex-1 text-gray-300">
                <span class="text-green-400 font-bold">{{ $log->user->nombres ?? 'Unknown' }}</span>
                logged in from <span class="text-gray-400">{{ $log->host }}</span>
                @if($log->logout_fecha)
                    <span class="text-gray-500 ml-2">(Duration: {{ gmdate("H:i:s", $log->duration_seconds) }})</span>
                @else
                    <span class="text-green-500 ml-2 animate-pulse">● Active</span>
                @endif
            </div>
        </div>
    @endforeach
     @if($logs->isEmpty())
        <div class="text-gray-500 text-center py-4">-- No logins found --</div>
    @endif
</div>
