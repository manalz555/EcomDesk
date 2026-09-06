<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Workspace-wide settings (company branding, SMTP, theme...). Deliberately
 * modeled as a single row rather than key/value pairs — see current().
 */
class WorkspaceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'logo_path',
        'primary_color',
        'business_hours',
        'notification_prefs',
        'smtp_config',
        'security_config',
        'default_theme',
    ];

    protected function casts(): array
    {
        return [
            'business_hours' => 'array',
            'notification_prefs' => 'array',
            // Same TEXT-column-not-JSON reasoning as ChannelIntegration::config —
            // encrypted ciphertext is not valid JSON.
            'smtp_config' => 'encrypted:array',
            'security_config' => 'array',
        ];
    }

    /** Parametres de l'espace de travail (ligne unique, creee si absente). */
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    public function logoUrl(): ?string
    {
        return $this->logo_path ? Storage::disk('public')->url($this->logo_path) : null;
    }
}
