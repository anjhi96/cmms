<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineProblem extends Model
{
    protected $fillable = [
    'machine_type',
    'problem'
];
}
