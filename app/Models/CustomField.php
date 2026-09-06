<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The *definition* of an admin-configurable client field (e.g. "Numero de
 * commande", type=text). The actual per-client values live in
 * CustomFieldValue — this is the EAV pattern, used so admins can add fields
 * without a migration for every new one.
 */
class CustomField extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'key',
        'type',
        'options',
    ];

    protected function casts(): array
    {
        return [
            // Only meaningful when type = 'select' — the list of choices.
            'options' => 'array',
        ];
    }

    public function values(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class);
    }
}
