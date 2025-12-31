<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable; // Importante para integração Stripe
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, Billable;

    // Atualizado para incluir 'role' e 'affiliate_code' das migrations anteriores
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'role', 
        'status', 
        'affiliate_code', 
        'referred_by'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // 'role' e 'status' são strings (Enum), não precisam de cast booleano
    ];

    /**
     * Controle de acesso ao painel Filament
     * Verifica se o usuário tem a role 'admin'
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin') && $this->status === 'active';
    }

    /* -------------------------------------------------------------------------- */
    /* RBAC (Roles & Permissions)                   */
    /* -------------------------------------------------------------------------- */

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        return !! $role->intersect($this->roles)->count();
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        $this->roles()->syncWithoutDetaching($role);
    }

    /* -------------------------------------------------------------------------- */
    /* RELACIONAMENTOS                              */
    /* -------------------------------------------------------------------------- */

    public function enrollments() { return $this->hasMany(Enrollment::class); }
    
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'enrollments')
            ->withTimestamps()
            ->withPivot('progress_percentage', 'completed_at', 'enrolled_at');
    }
    
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Sistema de Afiliados: Quem indicou este usuário
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * Sistema de Afiliados: Quem este usuário indicou
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /* -------------------------------------------------------------------------- */
    /* HELPERS                                  */
    /* -------------------------------------------------------------------------- */

    /**
     * Get or create wallet for user
     * Mantendo HTG como default conforme sua regra para o Haiti
     */
    public function getOrCreateWallet()
    {
        return $this->wallet()->firstOrCreate([], [
            'balance' => 0,
            'currency' => 'HTG',
            'status' => 'active'
        ]);
    }

    /**
     * Verificação de Assinatura
     * O Laravel Cashier já fornece métodos como $user->subscribed('default')
     * Mas mantemos o seu helper customizado se preferir lógica própria.
     */
    public function hasActiveSubscription(): bool
    {
        // Se usar Cashier: return $this->subscribed('default');
        return $this->subscriptions()->where('stripe_status', 'active')->exists();
    }
    
    /**
     * Check if user has enrollment for a specific course
     */
    public function hasEnrollment(int $courseId): bool
    {
        return $this->enrollments()
            ->where('course_id', $courseId)
            ->exists();
    }
    
    /**
     * Check if user is enrolled in a course
     */
    public function isEnrolledIn(Course $course): bool
    {
        return $this->hasEnrollment($course->id);
    }
}