<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * A staff account (admin, manager or agent) — also used to represent the
 * automation "bot" that drafts AI replies (see AutomationRule::bot()).
 */
class User extends Authenticatable implements MustVerifyEmail
{
    // MustVerifyEmailTrait provides markEmailAsVerified(); the interface above
    // is what tells Laravel's `verified` middleware to enforce it.
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    public const ROLES = ['admin', 'manager', 'agent'];

    // email_verified_at must stay fillable so an admin creating an account
    // from the UI can mark it verified immediately — omitting it here makes
    // Eloquent silently drop the field instead of raising an error.
    protected $fillable = [
        'name',
        'prenom',
        'email',
        'password',
        'role',
        'actif',
        'is_bot',
        'avatar_path',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // 'hashed' makes Eloquent bcrypt the password automatically on
            // save — plain-text passwords never touch the database.
            'password' => 'hashed',
            'actif' => 'boolean',
            'is_bot' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isAgent(): bool
    {
        return $this->role === 'agent';
    }

    /** Un manager herite des droits de supervision d'equipe d'un admin (hors reglages sensibles). */
    public function canManageTeam(): bool
    {
        return $this->isAdmin() || $this->isManager();
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    /** Conversations assignees a cet agent */
    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'agent_id');
    }

    /** Reponses redigees par cet agent */
    public function reponses(): HasMany
    {
        return $this->hasMany(Reponse::class, 'agent_id');
    }
}
