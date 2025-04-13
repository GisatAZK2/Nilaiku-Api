@php

use Illuminate\Support\Str;


    $url = Storage::url($getState()); // pastikan filesystem 'public' di-link
    $extension = Str::lower(pathinfo($url, PATHINFO_EXTENSION));


@endphp

<div class="space-y-2">
    @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
        <img src="{{ $url }}" alt="Report Image" class="rounded-xl shadow w-32 h-32 object-cover border border-gray-200">
    @elseif ($extension === 'pdf')
        <iframe src="{{ $url }}" class="w-60 h-48 border rounded-lg shadow" frameborder="0"></iframe>
    @else
        <div class="text-gray-500 text-sm italic">Tidak bisa dipreview ({{ $extension }})</div>
    @endif

    <div class="flex items-center gap-4 text-sm">
        <a href="{{ $url }}" target="_blank" class="text-blue-600 font-semibold hover:underline">📂 Lihat</a>
        <a href="{{ $url }}" download class="text-green-600 font-semibold hover:underline">⬇️ Download</a>
    </div>
</div>