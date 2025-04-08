<x-filament::widget>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Card: Welcome and Clock -->
        <x-filament::card>
                <div 
                    x-data="{ time: (new Date()).toLocaleTimeString() }"
                    x-init="setInterval(() => time = (new Date()).toLocaleTimeString(), 1000)"
                    class="flex flex-col space-y-2"
                >
                    <h2 class="text-xl font-bold text-gray-800">
                        Selamat datang, {{ ucfirst(explode('.', explode('@', auth()->user()->email)[0])[0]) }}! 👋
                    </h2>

                    <p class="text-lg text-gray-600">
                        Jam sekarang: <span x-text="time" class="font-mono text-blue-600"></span>
                    </p>

                    <x-filament::button
    tag="a"
    href="/api/documentation"
    target="_blank"
    color="primary"
    icon="heroicon-o-book-open"
>
    Baca Dokumentasi API
</x-filament::button>

</div>
        </x-filament::card>

        <!-- Card: Server Info -->
        <x-filament::card>
            <h2 class="text-lg font-bold mb-4">🧠 Info Sistem Server</h2>
            <ul class="space-y-2 text-sm">
                <li>🖥️ OS: {{ $os }}</li>
                <li>🧪 Total RAM: {{ $ram }}</li>
                <li>💾 Disk: {{ $disk }}</li>
            </ul>
        </x-filament::card>
    </div>
</x-filament::widget>
