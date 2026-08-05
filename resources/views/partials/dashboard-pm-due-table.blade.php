<div class="bg-white rounded-xl shadow-sm p-4 overflow-x-auto">
    <div class="text-sm font-medium text-gray-700 mb-4">PM Due (Next 7 Days)</div>
    <table class="w-full text-left text-sm">
        <thead class="text-gray-500">
            <tr>
                <th class="pb-2">Area</th>
                <th class="pb-2">Due Today</th>
                <th class="pb-2">+1 Day</th>
                <th class="pb-2">+2 Day</th>
                <th class="pb-2">Total Next 7 Days</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($rows as $row)
                <tr class="h-12">
                    <td class="py-2">{{ $row['area'] }}</td>
                    <td class="py-2">{{ $row['day0'] }}</td>
                    <td class="py-2">{{ $row['day1'] }}</td>
                    <td class="py-2">{{ $row['day2'] }}</td>
                    <td class="py-2 font-medium">{{ $row['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>