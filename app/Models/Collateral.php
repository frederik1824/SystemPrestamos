<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collateral extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'type',
        'description',
        'estimated_value',
        'status',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
