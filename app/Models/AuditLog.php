<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Casts\LowerCast;
use App\Models\Casts\SmimeCast;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\AuditLog
 *
 * @property string $email
 * @property SmimeCast $request
 * @property Carbon $created_at
 * @property string $event_code
 * @property string $action_code
 * @property bool $allowed_admin_view
 * @property bool $failed
 * @method static Builder|AuditLog newModelQuery()
 * @method static Builder|AuditLog newQuery()
 * @method static Builder|AuditLog query()
 * @method static Builder|AuditLog whereActionCode($value)
 * @method static Builder|AuditLog whereAllowedAdminView($value)
 * @method static Builder|AuditLog whereCreatedAt($value)
 * @method static Builder|AuditLog whereEmail($value)
 * @method static Builder|AuditLog whereEventCode($value)
 * @method static Builder|AuditLog whereFailed($value)
 * @method static Builder|AuditLog whereRequest($value)
 * @mixin Eloquent
 */
class AuditLog extends Model
{
    /**
     * @var string
     */
    protected $table = 'audit_logs';

    // No PK
    public $primaryKey = null;
    public $incrementing = false;

    // No timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'email',
        'request',
        'created_at',
        'event_code',
        'action_code',
        'allowed_admin_view',
        'failed',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'email' => LowerCast::class,
        'request' => SmimeCast::class,
    ];
}
