<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Dyrynda\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\InstituteLocation;
use App\Models\Application;
use App\Models\Property\Property;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Scopes\HideDeveloperRoleUsersScope;
use App\Support\StudentCountryList;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia, SoftDeletes, CascadeSoftDeletes;

    public const ACCOUNT_STATUS_ACTIVE = 'active';

    public const ACCOUNT_STATUS_PENDING = 'pending';

    public const ACCOUNT_STATUS_REJECTED = 'rejected';

    public const ACCOUNT_STATUS_UNVERIFIED = 'unverified';

    /**
     * Slug stored on users.role. "admin" here means an administration/staff account
     * (Vuexy); client portals use other contexts and Spatie guards (student, landlord, …).
     */
    public const ROLE_ADMIN = 'admin';

    public const ROLE_STUDENT = 'student';

    public const ROLE_LANDLORD = 'landlord';

    public const ROLE_AGENT = 'agent';

    protected $cascadeDeletes = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Prefix 'UID' to the 'userid' attribute
            $user->userid = 'UID' . $user->userid;
        });
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new HideDeveloperRoleUsersScope);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png'])
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(50)
                    ->height(50);
                $this->addMediaConversion('profile')
                    ->width(100)
                    ->height(100);
                $this->addMediaConversion('profile_view')
                    ->width(500)
                    ->height(500);
                $this->addMediaConversion('black_and_white')
                    ->greyscale()
                    ->quality(100);
            });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'userid',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'password',
        'phone',
        'role',
        'account_status',
        'dob',
        'institution_id',
        'country_code',
        'institute_location_id',
        'whatsapp',
        'student_id_number',
        'course_level',
        'graduation_year',
        'company_name',
        'billing_address',
        'agency_name',
        'license_number',
        'office_address',
        'job_title',
        'preferences',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
        'dob' => 'date',
        'graduation_year' => 'integer',
        'developer_anchor' => 'boolean',
        'super_admin_anchor' => 'boolean',
        'preferences' => 'array',
    ];

    /**
     * Display name (no `name` column — use first, middle, last).
     */
    public function getNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name ?? null,
            $this->middle_name ?? null,
            $this->last_name ?? null,
        ], fn ($p) => $p !== null && $p !== '');

        return implode(' ', $parts);
    }

    /**
     * Student nationality label from static ISO list (resources/data/countries.json).
     */
    public function getStudentCountryNameAttribute(): ?string
    {
        return StudentCountryList::nameForCode($this->country_code);
    }

    public function instituteRepresentatives(): HasMany
    {
        return $this->hasMany(InstituteRepresentative::class);
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'institution_id');
    }

    public function instituteLocation(): BelongsTo
    {
        return $this->belongsTo(InstituteLocation::class, 'institute_location_id');
    }

    /**
     * Published listings saved by this user (student wishlist).
     */
    public function savedProperties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'saved_properties')->withTimestamps();
    }

    /**
     * Property booking applications (student).
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'user_id');
    }

    /**
     * Filter by Spatie role name (e.g. "Landlord", "Student", "Agent").
     *
     * Kept separate from Spatie's scopeRole() so User::role(...) continues to work.
     */
    public function scopeWhereRoleName(Builder $query, string $roleName): Builder
    {
        return $query->whereHas('roles', function (Builder $q) use ($roleName) {
            $q->where('name', $roleName);
        });
    }

    /**
     * Filter by account_status (pending, rejected, unverified, active).
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('account_status', $status);
    }

    /**
     * Staff roles used for broad "administration" user lists.
     *
     * @param  array<int, string>  $roleNames
     */
    public function scopeWhereRolesIn(Builder $query, array $roleNames): Builder
    {
        return $query->whereHas('roles', function (Builder $q) use ($roleNames) {
            $q->whereIn('name', $roleNames);
        });
    }

    /**
     * Staff may use the Vuexy administration area (Spatie roles registered with guard `web`).
     */
    public function hasAdministrationAccess(): bool
    {
        return $this->roles->contains(fn ($role) => $role->guard_name === 'web');
    }

    /**
     * Whether the user has the Student Spatie role (works when logged in via `web` or `student` guard).
     */
    public function hasStudentRole(): bool
    {
        return $this->roles()->where('name', 'Student')->exists();
    }

    /**
     * Primary client portal URL for login redirect and when access to administration is denied.
     */
    public function clientPortalHomeUrl(): string
    {
        if ($this->hasStudentRole()) {
            return route('client.student.dashboard');
        }
        if ($this->hasRole('Landlord')) {
            return route('client.landlord.dashboard');
        }
        if ($this->hasRole('Agent')) {
            return route('client.agent.dashboard');
        }
        if ($this->hasRole('Institute Representative')) {
            return route('client.institute.dashboard');
        }

        return route('client.home');
    }
}
