<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function hasPermission(string $permission): bool
    {
        // Əlaqəli rolu və onun icazələrini yoxlayırıq
        if (!$this->role) {
            return false;
        }

        // Super Admin hər şeyi görsün
        if ($this->role->name === 'super_admin') {
            return true;
        }

        // İcazələri yoxla (pluck ilə adları alıb massivdə axtarırıq)
        return $this->role->permissions->pluck('name')->contains($permission);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role !== null;
    }
    
}
