<div class="bg-white rounded-xl shadow-sm p-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm text-gray-500">PM Completion</div>
            <div class="mt-1 text-3xl font-bold text-gray-800">{{ $percentage ?? '72%' }}</div>
        </div>
        <div class="text-right text-sm text-gray-500">
            <div>Target: <span class="text-gray-800 font-medium">{{ $target ?? 120 }}</span></div>
            <div>Actual: <span class="text-gray-800 font-medium">{{ $actual ?? 86 }}</span></div>
            <div>Remaining: <span class="text-gray-800 font-medium">{{ $remaining ?? 34 }}</span></div>
        </div>
    </div>

    <div class="mt-6">
        <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden">
            <div class="h-4 bg-green-400 rounded-full" style="width: {{ $percent_value ?? '72' }}%"></div>
        </div>
    </div>
</div>