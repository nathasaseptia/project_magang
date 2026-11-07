<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'uid',
        'up3',
        'ulp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Tingkat scope data dari tertinggi ke terendah: UID > UP3 > ULP.
     * Mengembalikan ['level' => 'uid'|'up3'|'ulp', 'value' => string|null]
     */
    public function dataScope(): array
    {
        if ($this->uid) {
            return ['level' => 'uid', 'value' => $this->uid];
        }
        if ($this->up3) {
            return ['level' => 'up3', 'value' => $this->up3];
        }
        if ($this->ulp) {
            return ['level' => 'ulp', 'value' => $this->ulp];
        }
        return ['level' => 'uid', 'value' => null]; // default lihat semua
    }

    /**
     * Apakah user berhak melihat data gangguan (bukan super admin).
     * Super admin hanya mengelola sistem, tidak melihat dashboard unit.
     */
    public function canViewUnitData(): bool
    {
        return !$this->isSuperAdmin();
    }
}
