<?php

namespace App\Imports;

use App\Models\MachineProblemFinding;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use App\Imports\MachineProblemFindingImport;
use Maatwebsite\Excel\Facades\Excel;

class MachineProblemFindingImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        $exists = MachineProblemFinding::where('category', trim($row['category']))
            ->where('finding', trim($row['finding']))
            ->first();

        if ($exists) {
            return null;
        }

        return new MachineProblemFinding([
            'category' => trim($row['category']),
            'finding'  => trim($row['finding']),
        ]);
    }

    public function rules(): array
    {
        return [
            '*.category' => 'required',
            '*.finding'  => 'required',
        ];
    }
}
