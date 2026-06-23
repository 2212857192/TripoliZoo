<?php

namespace App\Models;

use App\Enums\AppRole;
use App\Enums\Portal;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'assigned_group',
        'animal_group_id',
        'joined_at',
    ];

    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            if ($user->isDirty('animal_group_id')) {
                if ($user->animal_group_id) {
                    $user->assigned_group = AnimalGroup::query()
                        ->whereKey($user->animal_group_id)
                        ->value('name');
                } else {
                    $user->assigned_group = null;
                }
            } elseif ($user->isDirty('assigned_group') && filled($user->assigned_group)) {
                $user->animal_group_id = AnimalGroup::query()
                    ->where('name', $user->assigned_group)
                    ->value('id');
            }
        });
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'joined_at' => 'date',
        ];
    }

    public function animalGroup(): BelongsTo
    {
        return $this->belongsTo(AnimalGroup::class);
    }

    public function roleEnum(): ?UserRole
    {
        return UserRole::tryFrom($this->role);
    }

    public function portal(): ?Portal
    {
        return Portal::tryFromRole($this->roleEnum());
    }

    public function homePath(): string
    {
        return $this->roleEnum()?->homePath() ?? '/login';
    }

    public function hasPortal(Portal $portal): bool
    {
        return $this->portal() === $portal;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSystemAdmin(): bool
    {
        return $this->role === UserRole::SystemAdmin->value;
    }

    public function isVisitor(): bool
    {
        return $this->role === UserRole::Visitor->value;
    }

    public function canUseMobileApp(): bool
    {
        return $this->roleEnum()?->canUseMobileApp() ?? false;
    }

    public function canUseWebPortal(): bool
    {
        return $this->roleEnum()?->canUseWebPortal() ?? false;
    }

    public function appRole(): ?AppRole
    {
        return $this->roleEnum()?->appRole();
    }

    /** حسابات الموظفين (كل users ما عدا مدير النظام والزائر) */
    public function scopeEmployees(Builder $query): Builder
    {
        return $query->whereNotIn('role', [
            UserRole::SystemAdmin->value,
            UserRole::Visitor->value,
        ]);
    }

    public function isEmployeeAccount(): bool
    {
        return ! in_array($this->role, [
            UserRole::SystemAdmin->value,
            UserRole::Visitor->value,
        ], true);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(AdminActivityLog::class);
    }

    public function ticketSales(): HasMany
    {
        return $this->hasMany(TicketSale::class, 'sold_by');
    }

    public function quarantineNotifications(): HasMany
    {
        return $this->hasMany(QuarantineNotification::class);
    }

    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function isVetHead(): bool
    {
        return $this->role === UserRole::VetHead->value;
    }

    public function isVeterinarian(): bool
    {
        return $this->role === UserRole::Veterinarian->value;
    }

    public function isGroupSupervisor(): bool
    {
        return $this->role === UserRole::GroupSupervisor->value;
    }

    public function supervisesAnimalGroup(?string $group): bool
    {
        return $this->isGroupSupervisor()
            && $group !== null
            && $this->assigned_group === $group;
    }

    public function receivingTasks(): HasMany
    {
        return $this->hasMany(ReceivingTask::class, 'supervisor_id');
    }

    public function supervisorNotifications(): HasMany
    {
        return $this->hasMany(SupervisorNotification::class);
    }

    public function careNotifications(): HasMany
    {
        return $this->hasMany(CareNotification::class);
    }

    public function vetNotifications(): HasMany
    {
        return $this->hasMany(VetNotification::class);
    }

    public function canAccessVetPortal(): bool
    {
        return $this->hasPortal(Portal::Vet);
    }

    public function managesAnimalGroup(?string $group): bool
    {
        if ($this->isVetHead()) {
            return true;
        }

        return $this->isVeterinarian()
            && $group !== null
            && $this->assigned_group === $group;
    }
}
