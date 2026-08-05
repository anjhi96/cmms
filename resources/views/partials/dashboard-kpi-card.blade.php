<div class="bg-white rounded-xl shadow-sm p-4 flex items-center space-x-4">
    <div class="p-3 rounded-lg bg-indigo-50 text-indigo-600">
        {{-- Icon slot --}}
        {!! $icon ?? '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1 4v1m0-5V7m0 0h.01"></path></svg>' !!}
    </div>
    <div class="flex-1">
        <div class="text-sm text-gray-500">{{ $title }}</div>
        <div class="mt-1 text-2xl font-semibold text-gray-800">{{ $value }}</div>
    </div>
</div>