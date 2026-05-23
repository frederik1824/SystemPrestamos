<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'amount',
        'payment_date',
        'payment_method',
        'observations',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    protected static function booted()
    {
        static::created(function ($payment) {
            $loan = $payment->loan;
            if ($loan->type === 'open') {
                $loan->balance = (float)$loan->balance - (float)$payment->amount;
                if ($loan->balance <= 0) {
                    $loan->balance = 0;
                    $loan->status = 'paid';
                }
                $loan->save();
            } else {
                $loan->updateBalance();
            }
        });

        static::deleted(function ($payment) {
            $loan = $payment->loan;
            if ($loan->type === 'open') {
                $loan->balance = (float)$loan->balance + (float)$payment->amount;
                if ($loan->balance > 0 && $loan->status === 'paid') {
                    $loan->status = 'active';
                }
                $loan->save();
            } else {
                $loan->updateBalance();
            }
        });
    }
}
