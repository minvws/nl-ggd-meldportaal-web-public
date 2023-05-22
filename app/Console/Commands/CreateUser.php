<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Role;
use App\Services\Google2FA;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {email} {name} {password} {--role=*} {--serial=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user with specific roles';

    /**
     * @var Google2FA
     */
    protected $google2fa;

    /**
     * Create a new command instance.
     *
     * @param Google2FA $google2fa
     */
    public function __construct(Google2FA $google2fa)
    {
        parent::__construct();

        $this->google2fa = $google2fa;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $passwd = strval($this->argument('password'));

        $roles = $this->option('role');
        if (empty($roles) || !is_array($roles)) {
            $roles = [ Role::USER ];
        }

        // Validate roles and convert to upper case
        $roles = array_map(function ($role) {
            if (!is_string($role)) {
                throw new RuntimeException("Invalid role: $role");
            }

            $role = strtoupper($role);
            if (!in_array($role, Role::all())) {
                throw new Exception("Invalid role: $role");
            }

            return $role;
        }, $roles);

        $user = User::whereEmail($this->argument('email'))->first();
        if (is_null($user)) {
            $user = User::create([
                "email" => $this->argument('email'),
                "name" => $this->argument('name'),
                "password" => Hash::make($passwd),
                "roles" => $roles,
            ]);
        }

        if ($this->option('serial')) {
            $user->uzi_serial = strval($this->option('serial'));
        }

        $user->forceFill([
            'two_factor_secret' => encrypt($this->google2fa->generateSecretKey()),
            'two_factor_recovery_codes' => null,
            'password_updated_at' => now()
        ]);
        $user->save();

        $this->info('');
        $this->info('Email: ' . $this->argument('email'));
        $this->info('Password: ' . $passwd);
        $this->info('Authenticator: ' . $user->twoFactorQrCodeUrl());
        $this->info('');

        return 0;
    }
}
