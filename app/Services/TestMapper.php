<?php

declare(strict_types=1);

namespace App\Services;

use App\Elements;
use Exception;
use Illuminate\Support\Carbon;

class TestMapper
{
    public static function fromGgdTest(array $test): array
    {
        $data = [
            'initials' => $test['initials'] ?? '',
            'insertion' => $test['insertion'] ?? '',
            'surname' => $test['surname'] ?? '',
            'street' => $test['street'] ?? '',
            'house_number' => $test['house_number'] ?? '',
            'house_letter' => $test['house_letter'] ?? '',
            'house_number_addition' => $test['house_number_addition'] ?? '',
            'house_number_designation' => $test['house_number_designation'] ?? '',
            'postcode' => $test['postcode'] ?? '',
            'city' => $test['city'] ?? '',
            'country' => $test['country'] ?? '',
            'gender' => self::mapGender($test['gender'] ?? ''),
            'birthdate' => self::mapBirthDate($test['birthdate'] ?? ''),
            'bsn' => self::mapBsn($test['bsn'] ?? ''),
            'email' => $test['email'] ?? '',
            'phone' => $test['phone'] ?? '',
            'report_permission_gp' => self::mapYesOrNo($test['report_permission_gp'] ?? ''),
            'requesting_physician' => $test['requesting_physician'] ?? '',
            'brand_used_test' => $test['brand_used_test'] ?? '',
            'involved_laboratory' => $test['involved_laboratory'] ?? '',
            'category_test_location' => self::mapCategoryTestLocation($test['category_test_location'] ?? ''),
            'involved_company' => $test['involved_company'] ?? '',
            'test_after_contact_tracing' => self::mapYesOrNo($test['test_after_contact_tracing'] ?? ''),
            'bco_number' => $test['bco_number'] ?? '',
            'return_from_trip' => self::mapYesOrNo($test['return_from_trip'] ?? ''),
            'country_stay' => $test['country_stay'] ?? '',
            'flight_number' => $test['flight_number'] ?? '',
            'current_symptoms' => self::mapCurrentSymptoms($test['current_symptoms'] ?? ''),
            'first_day_of_illness_known' => self::mapFirstDayOfIllnessKnown($test['first_day_of_illness_known'] ?? ''),
            'first_day_of_illness_date' => self::mapDate($test['first_day_of_illness_date'] ?? null),
            'forwarded_by_coronamelder_app' => self::mapYesOrNo($test['forwarded_by_coronamelder_app'] ?? ''),
            'date_of_notification_coronamelder_app' =>
                self::mapDate($test['date_of_notification_coronamelder_app'] ?? ''),
            'date_of_contact_coronamelder_app' => self::mapDate($test['date_of_contact_coronamelder_app'] ?? ''),
            'last_two_weeks_worked_as_at_in' =>
                self::mapLastTwoWeeksWorkedAsAtIn($test['last_two_weeks_worked_as_at_in'] ?? ''),
            'caregiver_type' => self::mapCareGiverType($test['caregiver_type'] ?? ''),
            'contact_profession' => self::mapContactProfession($test['contact_profession'] ?? ''),
            'patient_gp_client_vvt_or_risk_group' =>
                self::mapPatientType($test['patient_gp_client_vvt_or_risk_group'] ?? ''),
            'risk_group' => self::mapRiskGroup($test['risk_group'] ?? ''),
            'date_of_sample_collection' => self::mapDate($test['date_of_sample_collection'] ?? ''),
            'date_of_test_result' => self::mapDate($test['date_of_test_result'] ?? ''),
            'test_result' => self::mapTestResult($test['test_result'] ?? ''),
            'user_id' => $test['user_id'] ?? '',
        ];

        return $data;
    }

    protected static function mapYesOrNo(mixed $value): string
    {
        if (!is_string($value)) {
            return 'no';
        }

        if (strtolower($value) === 'ja') {
            return 'yes';
        }

        return 'no';
    }

    /**
     * Map birthdate DD-MM-YYYY to YYYY-MM-DD.
     * @param string $birthdate
     * @return string
     */
    protected static function mapBirthDate(mixed $birthdate): string
    {
        if (!is_string($birthdate)) {
            return '';
        }

        $date = explode('-', $birthdate);
        if (count($date) != 3) {
            return '';
        }

        return $date[2] . '-' . $date[1] . '-' . $date[0];
    }

    protected static function mapGender(mixed $value): string
    {
        if (!is_string($value)) {
            return 'unknown';
        }

        return match (strtolower($value)) {
            'man' => 'male',
            'vrouw' => 'female',
            'niet_gespecificeerd' => 'unspecified',
            default => 'unknown',
        };
    }

    protected static function mapBsn(mixed $bsn): string
    {
        if (!is_string($bsn)) {
            return '';
        }

        if ($bsn === '999999999') {
            return '';
        }

        return $bsn;
    }

    protected static function mapCategoryTestLocation(mixed $categoryTestLocation): string
    {
        if (!is_string($categoryTestLocation)) {
            return '';
        }

        $list = Elements::getList(Elements::TEST_LOCATIONS, locale: 'nl');

        return self::mapInFlippedList($categoryTestLocation, $list);
    }

    protected static function mapInFlippedList(mixed $value, array $list): string
    {
        if (!is_string($value)) {
            return '';
        }

        $flippedList = array_flip($list);
        return $flippedList[$value] ?? '';
    }

    protected static function mapFirstDayOfIllnessKnown(mixed $first_day_of_illness_known): string
    {
        if (!is_string($first_day_of_illness_known)) {
            return '';
        }

        return match (strtolower($first_day_of_illness_known)) {
            'bekend' => 'known',
            'geschat' => 'estimated',
            default => 'unknown',
        };
    }

    protected static function mapDate(mixed $date): string
    {
        if (!is_string($date)) {
            return '';
        }

        try {
            $carbon = Carbon::createFromFormat('d-m-Y h:i:s', $date);
            if ($carbon !== false) {
                return $carbon->format('Y-m-d H:i:s');
            }
        } catch (Exception) {
        }

        return $date;
    }

    protected static function mapTestResult(mixed $test_result): string
    {
        if (!is_string($test_result)) {
            return '';
        }

        return match (strtolower($test_result)) {
            'positief' => 'positive',
            'negatief' => 'negative',
            default => '',
        };
    }

    protected static function mapCurrentSymptoms(mixed $currentSymptoms): array
    {
        return self::mapCommaDelimitedStringToArrayValue(
            $currentSymptoms,
            Elements::getList(Elements::SYMPTOMS, locale: 'nl')
        );
    }

    protected static function mapLastTwoWeeksWorkedAsAtIn(mixed $lastTwoWeeksWorkedAsAtIn): array
    {
        return self::mapCommaDelimitedStringToArrayValue(
            $lastTwoWeeksWorkedAsAtIn,
            Elements::getList(Elements::ENVIRONMENT, locale: 'nl')
        );
    }

    protected static function mapCaregiverType(mixed $caregiverType): string
    {
        foreach (Elements::getList(Elements::CAREGIVER, locale: 'nl') as $k => $v) {
            if ($v == $caregiverType) {
                return $k;
            }
        }

        return '';
    }

    protected static function mapContactProfession(mixed $contactProfession): string
    {
        foreach (Elements::getList(Elements::CONTACT_PROFESSION, locale: 'nl') as $k => $v) {
            if ($v == $contactProfession) {
                return $k;
            }
        }

        return '';
    }

    protected static function mapPatientType(mixed $patientType): string
    {
        foreach (Elements::getList(Elements::PATIENT_TYPE, locale: 'nl') as $k => $v) {
            if ($v == $patientType) {
                return $k;
            }
        }

        return '';
    }

    protected static function mapRiskGroup(mixed $riskGroup): array
    {
        return self::mapCommaDelimitedStringToArrayValue(
            $riskGroup,
            Elements::getList(Elements::RISK_GROUP, locale: 'nl')
        );
    }

    protected static function mapCommaDelimitedStringToArrayValue(mixed $commaDelimitedValues, array $values): array
    {
        if (!is_string($commaDelimitedValues)) {
            return [];
        }

        $result = [];
        foreach ($values as $key => $value) {
            if (str_contains($commaDelimitedValues, $value)) {
                $result[] = $key;
            }
        }

        return $result;
    }
}
