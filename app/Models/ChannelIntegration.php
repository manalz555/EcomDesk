<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Connection state for one external channel (WhatsApp, Instagram, Telegram,
 * SMTP, OpenAI, OCR...) — one row per channel, created on demand by
 * IntegrationController via firstOrCreate().
 */
class ChannelIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'is_connected',
        'webhook_token',
        'config',
        'connected_at',
    ];

    protected function casts(): array
    {
        return [
            'is_connected' => 'boolean',
            // API keys/tokens are stored encrypted at rest. Note: the
            // `config` column is TEXT, not JSON — MySQL's JSON type demands
            // valid JSON, and encrypted ciphertext isn't valid JSON, so a
            // JSON column would reject every write.
            'config' => 'encrypted:array',
            'connected_at' => 'datetime',
        ];
    }
}
