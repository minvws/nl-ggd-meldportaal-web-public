<?php

declare(strict_types=1);

namespace App\Validators;

use App\Exceptions\MonsterException;
use App\Models\User;
use App\Services\BsnService;
use Atomescrochus\StringSimilarities\Compare;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Unidecode\Unidecode;

class BsnLookup
{
    protected BsnService $bsnService;

    public function __construct(BsnService $bsnService)
    {
        $this->bsnService = $bsnService;
    }

    /**
     * @param mixed $field
     * @param mixed $value
     * @param mixed $param
     * @param Validator $validator
     * @return bool
     * @throws \SodiumException
     */
    public function check($field, $value, $param, $validator): bool
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            return false;
        }

        $data = $validator->getData();
        if (array_key_exists('data', $data)) {  // Form data vs api
            return $this->bsnIsValid($field, $value, $param, $validator, $data['data'], 'data.', $user);
        }

        return $this->bsnIsValid($field, $value, $param, $validator, $data, '', $user);
    }

    /**
     * @param mixed $field
     * @param mixed $value
     * @param mixed $param
     * @param Validator $validator
     * @param array $data
     * @param string $fieldPrefix
     * @param User $user
     * @return bool
     * @throws \SodiumException
     */
    protected function bsnIsValid($field, $value, $param, $validator, $data, string $fieldPrefix, User $user): bool
    {
        $bsn = $value;
        $dob = $data['birthdate'];
        $surname = $data['surname'];

        // Don't check if something is empty
        if (empty($bsn) || empty($dob)) {
            $validator->errors()->add($fieldPrefix . 'bsn', 'BSN and birthdate are required');
            return true;
        }

        try {
            $info = $this->bsnService->getInfo($bsn, $dob, $user);
        } catch (MonsterException $exception) {
            $validator->errors()->add(
                $fieldPrefix . 'bsn',
                $exception->getErrorMessage()
            );
            return false;
        }

        if (empty($info)) {
            $validator->errors()->add($fieldPrefix . 'bsn', 'Cannot retrieve BSN info');
            return true;
        }

        try {
            $bsnName = self::flatten($info['surname'] ?? '');
            $enteredName = self::flatten($surname ?? '');
        } catch (Exception) {
            $validator->errors()->add($fieldPrefix . 'surname', 'Last name not in valid format');
            return false;
        }

        // Check if surname (fuzzy) matches
        $comparison = new Compare();
        $similarity = $comparison->jaroWinkler($bsnName, $enteredName);
        if ($similarity < config('validation.bsn_name_fuzzy_matching_fraction')) {
            $validator->errors()->add($fieldPrefix . 'surname', 'Last name does not match BSN info');
            return false;
        }

        return true;
    }

    /**
     * @param string $input
     * @return string
     * @throws Exception
     * Based on knowledge gained in BRBA field experiments.
     * https://github.com/minvws/nl-covid19-registration-backend/blob/main/app/harrie5/utils/fuzzy.py
     */
    private static function flatten(string $input): string
    {
        return str_replace('ij', 'y', strtolower(Unidecode::unidecode($input)));
    }
}
