<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\LowerCast;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\TwoFactorAuthenticatable;
use MinVWS\Logging\Laravel\Contracts\LoggableUser;
use Ramsey\Uuid\Uuid;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $password_updated_at
 * @property string|null $password
 * @property string|null $remember_token
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property array|null $roles
 * @property bool $active
 * @property string|null $uzi_number
 * @property int|null $created_by
 * @property Carbon|null $downloaded_at
 * @property string|null $exported_at
 * @property string|null $uuid
 * @property string|null $uzi_serial
 * @property array $features
 * @property Carbon|null $active_until
 * @property int $suspicious_activity
 * @property Carbon|null $last_login_at
 * @property Carbon|null $last_active_at
 * @property-read User|null $createdBy
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static Builder|User byRole(string $role)
 * @method static UserFactory factory(...$parameters)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User sortable($defaultParameters = null)
 * @method static Builder|User whereActive($value)
 * @method static Builder|User whereActiveUntil($value)
 * @method static Builder|User whereAdminCreatedBy($value)
 * @method static Builder|User whereAuthorizedUntil($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereCreatedBy($value)
 * @method static Builder|User whereDownloadedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEmailVerifiedAt($value)
 * @method static Builder|User whereExportedAt($value)
 * @method static Builder|User whereFeatures($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastLoginAt($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User wherePasswordUpdatedAt($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereRoles($value)
 * @method static Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder|User whereTwoFactorSecret($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUuid($value)
 * @method static Builder|User whereUziNumber($value)
 * @method static Builder|User whereLastActiveAt($value)
 * @mixin Eloquent
 */
class User extends \Illuminate\Foundation\Auth\User implements LoggableUser
{
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * @var string
     */
    protected $table = 'mp_users';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'roles',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'email' => LowerCast::class,
        'roles' => 'json',
        'password_updated_at' => 'datetime',
    ];

    /**
     * Attributes to be encrypted with sodium secretbox at rest.
     *
     * @var string[]
     */
    protected array $encrypted = [
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(
            function (User $user) {
                // Information used for sending initial credentials via courier
                $user->uuid = Uuid::uuid4()->toString();
            }
        );
    }

    /**
     * @param array|string $wantedRoles
     * @return bool
     */
    public function hasRole(array|string $wantedRoles): bool
    {
        $havingRoles = $this->roles ?? [];

        if (is_string($wantedRoles)) {
            $wantedRoles = [ $wantedRoles ];
        }

        // Make sure all are in uppercase
        $wantedRoles = array_map('strtoupper', $wantedRoles);

        return count(array_intersect($wantedRoles, $havingRoles)) >= 1;
    }

    public function twoFactorQrCodeSvgWithAria(): string
    {
        $svgTag = $this->twoFactorQrCodeSvg();
        return str_replace('<svg ', '<svg role="img"focusable="false" aria-label="QR-code" ', $svgTag);
    }

    /**
     * Get the two factor authentication QR code URL.
     * We don't use full email but only the part before the @.
     *
     * @return string
     */
    public function twoFactorQrCodeUrl(): string
    {
        return app(TwoFactorAuthenticationProvider::class)->qrCodeUrl(
            config('app.name'),
            substr($this->email, 0, (int)strpos($this->email, '@')),
            decrypt((string)$this->two_factor_secret)
        );
    }

    public function canChangePassword(): bool
    {
        return $this->isUzi() == false;
    }

    public function isUzi(): bool
    {
        return $this->uzi_serial !== null || $this->uzi_number !== null;
    }
}
