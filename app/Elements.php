<?php

declare(strict_types=1);

namespace App;

use Illuminate\Support\Facades\App;

/*
 * This class is a wrapper around the elements-<locale>.json files. It's used to make sure we can keep
 * the data for the backend/templates and javascript in sync as some language strings are used by
 * both the front and backend.
 */
class Elements
{
    public const CAREGIVER = "caregiver_type";
    public const CONTACT_PROFESSION = "contact_profession";
    public const ENVIRONMENT = "last_two_weeks_worked_as_at_in";
    public const RISK_GROUP = "risk_group";
    public const SYMPTOMS = "current_symptoms";
    public const TEST_BRANDS = "brand_used_test";
    public const TEST_LOCATIONS = "category_test_location";
    public const COUNTRIES = "country_stay";
    public const PATIENT_TYPE = "patient_gp_client_vvt_or_risk_group";

    protected static ?array $elements = null;

    public static function getList(string $group, ?string $locale = null): array
    {
        if ($locale == null) {
            $locale = App::currentLocale();
        }

        if (static::$elements === null || !isset(static::$elements[$locale])) {
            static::loadElements($locale);
        }

        return static::$elements[$locale][$group] ?? [];
    }

    public static function asJson(string $group, ?string $locale = null): string
    {
        $ret = json_encode(static::getList($group, $locale));
        if ($ret === false) {
            throw new \RuntimeException("Could not encode group $group ($locale) as JSON");
        }

        return $ret;
    }

    protected static function loadElements(string $locale): void
    {
        $json = file_get_contents(App::resourcePath('elements-' . $locale . '.json'));
        if ($json === false) {
            $json = "";
        }

        $data = json_decode($json, true, 512);
        if ($data === false) {
            throw new \RuntimeException("Could not decode elements-'.$locale.'.json");
        }

        static::$elements[$locale] = $data;
    }
}
