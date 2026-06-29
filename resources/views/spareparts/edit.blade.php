@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Edit Sparepart
            </h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6 max-w-4xl">

            <form action="{{ route('spareparts.update', $sparepart->id) }}" method="POST">

                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- MATERIAL NUMBER --}}
                    <div>
                        <label>Material Number</label>
                        <input type="text" name="material_number"
                            value="{{ old('material_number', $sparepart->material_number) }}"
                            class="w-full border p-2 rounded">
                    </div>

                    {{-- LOCATION --}}
                    <div>
                        <label>Location</label>
                        <input type="text" name="location" value="{{ old('location', $sparepart->location) }}"
                            class="w-full border p-2 rounded">
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="md:col-span-2">
                        <label>Description</label>
                        <input type="text" name="description" value="{{ old('description', $sparepart->description) }}"
                            class="w-full border p-2 rounded">
                    </div>

                    {{-- REMARKS --}}
                    <div class="md:col-span-2">
                        <label>Remarks</label>
                        <textarea name="remarks" class="w-full border p-2 rounded">{{ old('remarks', $sparepart->remarks) }}</textarea>
                    </div>

                    {{-- STOCK --}}
                    <div>
                        <label>Stock</label>
                        <input type="number" step="0.01" name="stock" value="{{ old('stock', $sparepart->stock) }}"
                            class="w-full border p-2 rounded">
                    </div>

                    {{-- UNIT --}}
                    <div>
                        <label>Unit</label>
                        <input type="text" name="unit" value="{{ old('unit', $sparepart->unit) }}"
                            class="w-full border p-2 rounded">
                    </div>

                    {{-- ROP --}}
                    <div>
                        <label>ROP</label>
                        <input type="number" name="rop" value="{{ old('rop', $sparepart->rop) }}"
                            class="w-full border p-2 rounded">
                    </div>

                    {{-- MRP TYPE --}}
                    <div>
                        <label>MRP Type</label>
                        <input type="text" name="mrp_type" value="{{ old('mrp_type', $sparepart->mrp_type) }}"
                            class="w-full border p-2 rounded">
                    </div>

                    {{-- PRICE --}}
                    <div>
                        <label>Price</label>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $sparepart->price) }}"
                            class="w-full border p-2 rounded">
                    </div>

                    {{-- STATUS --}}
                    <div>
                        <label>Status</label>

                        <select name="status" class="w-full border p-2 rounded">

                            <option value="ACTIVE" {{ $sparepart->status == 'ACTIVE' ? 'selected' : '' }}>
                                ACTIVE
                            </option>

                            <option value="INACTIVE" {{ $sparepart->status == 'INACTIVE' ? 'selected' : '' }}>
                                INACTIVE
                            </option>

                        </select>
                    </div>

                    {{-- MACHINE TYPE --}}
                    <div>
                        <label>Machine Type</label>

                        <select name="machine_type" class="w-full border p-2 rounded">

                            <option value="">Select Type</option>

                            @foreach ($machines as $machine)
                                <option value="{{ $machine->machine_type }}"
                                    {{ $sparepart->machine_type == $machine->machine_type ? 'selected' : '' }}>

                                    {{ $machine->machine_type }}

                                </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- SEGMENT --}}
                    <div>
                        <label>Segment</label>
                        <input type="text" name="segment" value="{{ old('segment', $sparepart->segment) }}"
                            class="w-full border p-2 rounded">
                    </div>

                    {{-- PDT --}}
                    <div>
                        <label>PDT</label>
                        <input type="text" name="pdt" value="{{ old('pdt', $sparepart->pdt) }}"
                            class="w-full border p-2 rounded">
                    </div>

                </div>

                <div class="mt-6 flex gap-2">

                    <button class="bg-blue-600 text-white px-5 py-2 rounded">
                        Update
                    </button>

                    <a href="{{ route('spareparts.index') }}" class="bg-gray-400 text-white px-5 py-2 rounded">

                        Cancel

                    </a>

                </div>

            </form>

        </div>

    </div>
@endsection
