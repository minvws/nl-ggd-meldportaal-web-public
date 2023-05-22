<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use SodiumException;
use Symfony\Component\Console\Attribute\AsCommand;

/**
 * Command based on the Laravel key:generate command.
 */
#[AsCommand(name: 'crypto-keys:generate')]
class LocalGenerateCryptoKeysCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crypto-keys:generate
                    {--show : Display the key instead of modifying files}
                    {--force : Force the operation to run when in production}';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'crypto-keys:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the crypto keys for local development';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws SodiumException
     */
    public function handle(): void
    {
        $this->generateSecretsForDatabase();
        $this->generateSecretsForBackend();

        $this->info('Application crypto keys set successfully.');
    }

    /**
     * @throws SodiumException
     */
    protected function generateSecretsForBackend(): void
    {
        [$publicKey, $secretKey] = $this->generateSodiumKeys();
        if ($this->option('show')) {
            $this->info('Secrets for backend.');
            $this->line('Public key: <comment>' . $publicKey . '</comment>');
            $this->line('Secret key: <comment>' . $secretKey . '</comment>');
            $this->newLine();
            return;
        }

        $this->setKeyInEnvironmentFile('crypto.backend.public_key', 'BACKEND_PUBLIC_KEY', $publicKey);
        $this->setKeyInEnvironmentFile('crypto.backend.private_key', 'BACKEND_PRIVATE_KEY', $secretKey);
    }

    /**
     * @throws SodiumException
     */
    protected function generateSecretsForDatabase(): void
    {
        [$publicKey, $secretKey] = $this->generateSodiumKeys();
        if ($this->option('show')) {
            $this->info('Secrets for database.');
            $this->line('Public key: <comment>' . $publicKey . '</comment>');
            $this->line('Secret key: <comment>' . $secretKey . '</comment>');
            $this->newLine();
            return;
        }

        $this->setKeyInEnvironmentFile('crypto.database.public_key', 'DATABASE_PUBLIC_KEY', $publicKey);
        $this->setKeyInEnvironmentFile('crypto.database.private_key', 'DATABASE_PRIVATE_KEY', $secretKey);
    }

    /**
     * Generates sodium keys and returns public and secret key.
     * @returns array<string, string>
     * @throws SodiumException
     */
    protected function generateSodiumKeys(): array
    {
        $keypair = sodium_crypto_box_keypair();

        return [
            base64_encode(sodium_crypto_box_publickey($keypair)),
            base64_encode(sodium_crypto_box_secretkey($keypair)),
        ];
    }

    /**
     * Set the specific key in the environment file.
     *
     * @param string $configKey
     * @param string $envKey
     * @param string $value
     * @return bool
     */
    protected function setKeyInEnvironmentFile(string $configKey, string $envKey, string $value): bool
    {
        $currentValue = config($configKey);

        if (
            !empty($currentValue)
            && (! $this->confirmToProceed(
                warning: $envKey . ' already exists in your .env file. Do you want to replace it?',
                callback: true
            ))
        ) {
            return false;
        }

        if (! $this->writeNewEnvironmentFileWith($configKey, $envKey, $value)) {
            return false;
        }

        return true;
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param string $configKey
     * @param string $envKey
     * @param string $value
     * @return bool
     */
    protected function writeNewEnvironmentFileWith(string $configKey, string $envKey, string $value): bool
    {
        $input = file_get_contents($this->laravel->environmentFilePath());
        if (!is_string($input)) {
            return false;
        }

        $replaced = preg_replace(
            $this->keyReplacementPattern($configKey, $envKey),
            $envKey . '=' . $value,
            $input
        );

        if ($replaced === $input || $replaced === null) {
            $this->error('Unable to set application key. No ' . $envKey . ' variable was found in the .env file.');

            return false;
        }

        file_put_contents($this->laravel->environmentFilePath(), $replaced);

        return true;
    }

    /**
     * Get a regex pattern that will match the specified env with the current config value.
     *
     * @param string $configKey
     * @param string $envKey
     * @return string
     */
    protected function keyReplacementPattern(string $configKey, string $envKey): string
    {
        $escaped = preg_quote('=' . config($configKey), '/');

        return "/^$envKey$escaped/m";
    }
}
