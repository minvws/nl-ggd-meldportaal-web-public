<?php

declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LoginActivity
 *
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $logged_in_at
 * @property string|null $scope
 * @method static Builder|LoginActivity newModelQuery()
 * @method static Builder|LoginActivity newQuery()
 * @method static Builder|LoginActivity query()
 * @method static Builder|LoginActivity whereIpAddress($value)
 * @method static Builder|LoginActivity whereLoggedInAt($value)
 * @method static Builder|LoginActivity whereScope($value)
 * @method static Builder|LoginActivity whereUserId($value)
 * @mixin Eloquent
 */
class LoginActivity extends Model
{
    /**
     * @var string
     */
    protected $table = 'login_activity';

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
        'user_id',
        'ip_address',
        'logged_in_at',
        'scope',
    ];
}
