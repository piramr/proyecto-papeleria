<div class="space-y-2">
    @foreach($logs as $log)
        <div class="flex items-start text-xs sm:text-sm p-2 hover:bg-gray-800 rounded transition duration-150 border-l-2 {{ $log->response_code >= 400 ? 'border-red-500' : 'border-blue-500' }}">
            <div class="w-32 text-gray-500 flex-shrink-0">{{ $log->timestamp->format('H:i:s') }}</div>
            <div class="w-24 font-bold {{ $log->http_method == 'GET' ? 'text-blue-400' : ($log->http_method == 'POST' ? 'text-green-400' : 'text-yellow-400') }}">
                {{ $log->http_method }}
            </div>
            <div class="flex-1 text-gray-300 break-all">
                {{ $log->endpoint }}
                <span class="text-gray-600 block text-xs mt-1">User: {{ $log->user->nombres ?? 'Guest' }} | IP: {{ $log->ip_address }} | Code: {{ $log->response_code }}</span>
            </div>
        </div>
    @endforeach
    @if($logs->isEmpty())
        <div class="text-gray-500 text-center py-4">-- No logs found --</div>
    @endif
</div>
