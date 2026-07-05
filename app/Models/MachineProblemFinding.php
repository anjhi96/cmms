<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineProblemFinding extends Model
{
    protected $fillable = [
        'category',
        'finding',
    ];

    public function pmProblems()
    {
        return $this->hasMany(
            PMProblem::class,
            'machine_problem_finding_id'
        );
    }
}
