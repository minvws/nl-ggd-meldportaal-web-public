<?php

declare(strict_types=1);

namespace App\Services;

use App\LogEvents\ReportLogEvent;
use App\Models\Test;
use App\Models\User;
use MinVWS\Logging\Laravel\LogService;

class TestService
{
    protected BsnService $bsnService;
    protected MapTestBrandsToEuValuesService $euValuesMappingService;
    protected LogService $logService;

    public function __construct(
        BsnService $bsnService,
        MapTestBrandsToEuValuesService $euValuesMappingService,
        LogService $logService
    ) {
        $this->bsnService = $bsnService;
        $this->euValuesMappingService = $euValuesMappingService;
        $this->logService = $logService;
    }


    public function storeTest(array $data, User $user, bool $specimen = false): bool
    {
        $bsn = $data['bsn'] ?? '';
        $dob = $data['birthdate'] ?? '';

        // If we have a BSN, try to get the name and date of birth from the BSN service
        if (! empty($bsn)) {
            $info = $this->bsnService->getInfo($bsn, $dob, $user);
            if (empty($info) || !isset($info['firstNames'], $info['surname'], $info['date_of_birth'])) {
                return false;
            }

            $data = $this->correctSurname($info, $data);
            $data = $this->enrichFromMonster($info, $data);
            $data = $this->enrichWithEuEventValues($data);
        }

        $data['is_specimen'] = $specimen;

        // Create a new Test entry
        $test = Test::create($data);

        $this->logService->log((new ReportLogEvent())
            ->asCreate()
            ->withActor($user)
            ->withPiiData([
                'data' => $data,
                'specimen' => $specimen,
                'id' => $test->id,
                'last_active_at' => $user->last_active_at,
                'last_login_at' => $user->last_login_at,
            ]));


        return true;
    }

    protected function correctSurname(array $info, array $data): array
    {
        // We need to correct the surname based on the response from monster

        // Use the surname from monster
        $data['surname'] = $info['surname'];

        return $data;
    }

    protected function enrichFromMonster(array $info, array $data): array
    {
        // Enrich from monster
        $data['brp_first_names'] = $info['firstNames'];
        $data['brp_prefix_surname'] = $info['prefixSurname'] ?? null;
        $data['brp_surname'] = $info['surname'];
        $data['brp_date_of_birth'] = $info['date_of_birth'];

        return $data;
    }

    protected function enrichWithEuEventValues(array $data): array
    {
        [
            'eu_event_type' => $type,
            'eu_event_manufacturer' => $manufacturer,
            'eu_event_name' => $name,
        ] = $this->euValuesMappingService->getEuEventValuesForTestBrand($data['brand_used_test'] ?? '');

        $data['eu_event_type'] = $type;
        $data['eu_event_manufacturer'] = $manufacturer;
        $data['eu_event_name'] = $name;

        return $data;
    }
}
