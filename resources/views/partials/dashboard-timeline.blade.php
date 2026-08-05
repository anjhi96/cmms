<div class="bg-white rounded-xl shadow-sm p-4">
    <div class="text-sm font-medium text-gray-700 mb-4">Activity Timeline</div>
    <div class="space-y-4">
        @foreach($activities as $a)
            <div class="flex items-start space-x-3">
                <div class="text-xs text-gray-400">{{ $a['time'] }}</div>
                <div class="flex-1">
                    <div class="text-sm font-medium text-gray-800">{{ $a['machine'] }}</div>
                    <div class="text-sm text-gray-600">{{ $a['desc'] }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>