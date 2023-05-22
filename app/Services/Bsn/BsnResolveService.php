<?php

declare(strict_types=1);

namespace App\Services\Bsn;

use App\Exceptions\MonsterException;
use App\LogEvents\BsnLookupLogEvent;
use App\Models\User;
use App\Services\Monster\MonsterInterface;
use MinVWS\Logging\Laravel\LogService;

/**
 * Class BsnResolveService
 */
class BsnResolveService
{
    protected MonsterInterface $monster;
    protected LogService $logService;

    public function __construct(MonsterInterface $monster, LogService $logService)
    {
        $this->monster = $monster;
        $this->logService = $logService;
    }

    /**
     * Returns true when the BsnResolver service is healthy
     *
     * @return bool
     */
    public function isHealthy(): bool
    {
        return $this->monster->isHealthy();
    }

    /**
     * @param User $user
     * @param string $pubKey
     * @param string $encryptedBsn
     * @param string $dob
     * @return array
     * @throws MonsterException
     */
    public function resolveBsn(
        User $user,
        string $pubKey,
        string $encryptedBsn,
        string $dob
    ): array {
        $details = $this->getAdminDetails($user);

        $data = [
            'admin_email' => $details['adminEmail'],
            'admin_name' => $details['adminName'],
            'bsn' => $encryptedBsn,
            'user_email' => $details['email'],
            'user_name' => $details['name'],
            'user_role' => 'registrant',
            'date_of_birth' => $dob,
        ];

        $result = $this->monster->fetch($data, $pubKey);
        if (
            empty($result)
            || !array_key_exists('errors', $result)
            || !empty($result['errors'])
            || empty($result['data'])
        ) {
            $this->logEvent($user, $encryptedBsn, $dob, succeeded: false);
            throw new MonsterException(
                "Monster returned the following errors:" . json_encode($result['errors'] ?? ''),
                0,
                null,
                null,
                null,
                (array)$result['errors']
            );
        }

        $this->logEvent($user, $encryptedBsn, $dob, succeeded: true);

        return $this->filterResultData($result['data']);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function filterResultData(array $data): array
    {
        $allowedKeys = [
            'jwt',
            'firstNames',
            'surname',
            'prefixSurname',
            'location',
            'gender',
            'honorary_title',
            'postal_code',
            'infix_partner',
            'designation_name',
            'surname_partner',
            'street_name',
            'house_letter',
            'house_number',
            'house_number_additional',
            'info_house_number',
            'location_information',
            'country_address_foreign',
            'first_row_foreign_address',
            'second_row_foreign_address',
            'third_row_foreign_address',
            'date_of_birth',
        ];

        return array_filter($data, function ($key) use ($allowedKeys) {
            return in_array($key, $allowedKeys);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function logEvent(
        User $user,
        string $bsn,
        string $dob,
        bool $succeeded = true,
        string $error = ''
    ): void {
        $data = [
            'success' => $succeeded,
            'last_active_at' => $user->last_active_at,
            'last_login_at' => $user->last_login_at,
        ];

        if ($succeeded === false) {
            $data['error'] = $error;
        }

        $this->logService->log((new BsnLookupLogEvent())
            ->asExecute()
            ->withActor($user)
            ->withData($data)
            ->withPiiData([
                // Encrypted data
                'bsn' => $bsn,
                'date_of_birth' => $dob,
            ])
            ->withFailed($succeeded == false, $data['error'] ?? ''));
    }

    public function getAdminDetails(User $user): array
    {
        $name = $user->name;
        $email = $user->email;

        return [
            "email" => $email,
            "name" => $name,
            "adminEmail" => $email,
            "adminName" => $name,
        ];
    }
}
