<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** The business a Client belongs to (optional — a client can exist without a company). */
class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'phone',
        'notes',
    ];

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
