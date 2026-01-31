@props(['timeout' => config('session.lifetime') * 60])

<div x-data="sessionTimeout({{ $timeout }})"
     x-init="init()"
     style="display: none;"
     x-show="showWarning"
     class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75 transition-opacity">
    
    <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
        <div class="bg-red-50 px-4 py-3 sm:px-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-3 w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('La sesión está por expirar') }}
                    </h3>
                    <div class="mt-2 text-sm text-gray-500">
                        <p>
                            {{ __('Tu sesión expirará en') }} <span x-text="timeLeft" class="font-bold"></span> {{ __('segundos debido a inactividad.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="extendSession" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                {{ __('Extender Sesión') }}
            </button>
            <button @click="logout" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                {{ __('Cerrar Sesión') }}
            </button>
        </div>
    </div>
</div>

<script>
    function sessionTimeout(lifetimeSeconds) {
        return {
            showWarning: false,
            timeLeft: 60,
            timer: null,
            warningTimer: null,
            lifetime: lifetimeSeconds, // Total session lifetime in seconds

            init() {
                this.resetTimers();
                
                // Reset timers on user activity
                ['click', 'mousemove', 'keydown', 'scroll'].forEach(event => {
                    document.addEventListener(event, () => this.resetTimers());
                });
            },

            resetTimers() {
                if (this.showWarning) return; // Don't reset if warning is showing

                clearTimeout(this.timer);
                clearInterval(this.warningTimer);

                // Warning appears 60 seconds before expiration
                this.timer = setTimeout(() => {
                    this.showWarning = true;
                    this.startCountdown();
                }, (this.lifetime - 60) * 1000);
            },

            startCountdown() {
                this.timeLeft = 60;
                this.warningTimer = setInterval(() => {
                    this.timeLeft--;
                    if (this.timeLeft <= 0) {
                        this.logout();
                    }
                }, 1000);
            },

            extendSession() {
                // Ping server to keep session alive
                fetch('{{ route('keep-alive') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => {
                    this.showWarning = false;
                    this.resetTimers();
                });
            },

            logout() {
                window.location.href = "{{ route('logout.get') }}";
            }
        }
    }
</script>
