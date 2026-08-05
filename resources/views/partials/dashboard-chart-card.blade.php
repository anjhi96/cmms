<div class="bg-white rounded-xl shadow-sm p-4">
    <div class="flex items-center justify-between mb-4">
        <div class="text-sm font-medium text-gray-700">{{ $title }}</div>
        @if(isset($filters))
            <div class="space-x-2 text-sm text-gray-500">
                {!! $filters !!}
            </div>
        @endif
    </div>
    <div class="h-56 md:h-64">
        <canvas id="{{ $id }}"></canvas>
    </div>
</div>