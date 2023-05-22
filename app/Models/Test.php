<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\AsymEncrypted;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Test
 *
 * @property string $initials
 * @property string|null $insertion
 * @property string $surname
 * @property string $postcode
 * @property string $house_number
 * @property string|null $house_letter
 * @property string|null $house_number_addition
 * @property string|null $house_number_designation
 * @property string $street
 * @property string $city
 * @property string $gender
 * @property string $bsn
 * @property string $birthdate
 * @property string $email
 * @property string $phone
 * @property string|null $report_permission_gp
 * @property string|null $requesting_physician
 * @property string|null $brand_used_test
 * @property string|null $involved_laboratory
 * @property string|null $category_test_location
 * @property string|null $involved_company
 * @property string|null $test_after_contact_tracing
 * @property string|null $bco_number
 * @property string|null $return_from_trip
 * @property string|null $country_stay
 * @property string|null $flight_number
 * @property array|null $current_symptoms
 * @property string|null $first_day_of_illness_known
 * @property \Illuminate\Support\Carbon|null $first_day_of_illness_date
 * @property string|null $forwarded_by_coronamelder_app
 * @property array|null $last_two_weeks_worked_as_at_in
 * @property \Illuminate\Support\Carbon|null $date_of_notification_coronamelder_app
 * @property \Illuminate\Support\Carbon|null $date_of_contact_coronamelder_app
 * @property string|null $caregiver_type
 * @property string|null $contact_profession
 * @property string|null $patient_gp_client_vvt_or_risk_group
 * @property array|null $risk_group
 * @property \Illuminate\Support\Carbon $date_of_sample_collection
 * @property \Illuminate\Support\Carbon $date_of_test_result
 * @property string $test_result
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property string $id
 * @property bool|null $i7_synchronised
 * @property \Illuminate\Support\Carbon|null $i7_synchronised_at
 * @property bool|null $ggd_synchronised
 * @property \Illuminate\Support\Carbon|null $ggd_synchronised_at
 * @property string|null $country
 * @property string|null $brp_first_names
 * @property string|null $brp_prefix_surname
 * @property string|null $brp_surname
 * @property string|null $brp_date_of_birth
 * @property string|null $eu_event_type
 * @property string|null $eu_event_manufacturer
 * @property string|null $eu_event_name
 * @property bool $is_specimen
 * @method static \Database\Factories\TestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Test newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Test newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Test query()
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereBcoNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereBrandUsedTest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereBrpDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereBrpFirstNames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereBrpPrefixSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereBrpSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereBsn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereCaregiverType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereCategoryTestLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereContactProfession($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereCountryStay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereCurrentSymptoms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereDateOfContactCoronamelderApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereDateOfNotificationCoronamelderApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereDateOfSampleCollection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereDateOfTestResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereEuEventManufacturer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereEuEventName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereEuEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereFirstDayOfIllnessDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereFirstDayOfIllnessKnown($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereFlightNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereForwardedByCoronamelderApp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereGgdSynchronised($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereGgdSynchronisedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereHouseLetter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereHouseNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereHouseNumberAddition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereHouseNumberDesignation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereI7Synchronised($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereI7SynchronisedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereInitials($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereInsertion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereInvolvedCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereInvolvedLaboratory($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereLastTwoWeeksWorkedAsAtIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test wherePatientGpClientVvtOrRiskGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test wherePostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereReportPermissionGp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereRequestingPhysician($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereReturnFromTrip($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereRiskGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereTestAfterContactTracing($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereTestResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Test whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Test extends Model
{
    use HasFactory;
    use HasUuid;
    use AsymEncrypted;

    protected $table = 'mp_tests';

    protected $fillable = [
        'initials',
        'insertion',
        'surname',
        'postcode',
        'house_number',
        'house_letter',
        'house_number_addition',
        'house_number_designation',
        'street',
        'city',
        'country',
        'gender',
        'bsn',
        'birthdate',
        'email',
        'phone',
        'report_permission_gp',
        'requesting_physician',
        'brand_used_test',
        'involved_laboratory',
        'category_test_location',
        'involved_company',
        'test_after_contact_tracing',
        'bco_number',
        'return_from_trip',
        'country_stay',
        'flight_number',
        'current_symptoms',
        'first_day_of_illness_known',
        'first_day_of_illness_date',
        'forwarded_by_coronamelder_app',
        'last_two_weeks_worked_as_at_in',
        'caregiver_type',
        'date_of_notification_coronamelder_app',
        'date_of_contact_coronamelder_app',
        'contact_profession',
        'patient_gp_client_vvt_or_risk_group',
        'risk_group',
        'date_of_sample_collection',
        'date_of_test_result',
        'test_result',
        'i7_synchronised',
        'i7_synchronised_at',
        'ggd_synchronised',
        'ggd_synchronised_at',
        'brp_first_names',
        'brp_prefix_surname',
        'brp_surname',
        'brp_date_of_birth',
        'eu_event_type',
        'eu_event_manufacturer',
        'eu_event_name',
        'is_specimen',
    ];

    protected $attributes = [
        'forwarded_by_coronamelder_app' => 'no',
        'is_specimen' => false,
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'first_day_of_illness_date' => 'date',
        'date_of_notification_coronamelder_app' => 'date',
        'date_of_contact_coronamelder_app' => 'date',
        'date_of_sample_collection' => 'datetime',
        'date_of_test_result' => 'date',
        'current_symptoms' => 'array',
        'last_two_weeks_worked_as_at_in' => 'array',
        'risk_group' => 'array',
        'i7_synchronised' => 'boolean',
        'i7_synchronised_at' => 'datetime',
        'ggd_synchronised' => 'boolean',
        'ggd_synchronised_at' => 'datetime',
        'is_specimen' => 'boolean',
    ];

    /**
     * Attributes to be encrypted with sodium secretbox at rest.
     *
     * @var string[]
     */
    protected array $encrypted = [
        'initials',
        'insertion',
        'surname',
        'postcode',
        'house_number',
        'house_letter',
        'house_number_addition',
        'house_number_designation',
        'street',
        'city',
        'country',
        'gender',
        'bsn',
        'birthdate',
        'email',
        'phone',
        'requesting_physician',
        'involved_laboratory',
        'involved_company',
        'bco_number',
        'country_stay',
        'flight_number',
        'brp_first_names',
        'brp_prefix_surname',
        'brp_surname',
        'brp_date_of_birth',
    ];

    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->encrypted)) {
                try {
                    $attributes[$key] = $this->getAttribute($key);
                } catch (\Exception $e) {
                    // Can't decrypt data somehow. Just return as-is.
                }
            }
        }

        return $attributes;
    }
}
