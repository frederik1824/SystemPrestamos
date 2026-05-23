<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'identification_id',
        'photo_path',
        'phone',
        'address',
        'email',
        'references',
        'notes',
        'guarantee',
    ];

    protected $casts = [
        'references' => 'array',
    ];

    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=004a99&background=E2E8F0';
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
