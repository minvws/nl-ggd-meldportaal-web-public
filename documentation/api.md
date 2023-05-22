# API

Meldportaal has an API endpoint (/api/tests) where you can send a positive test result to.

## OpenAPI Specs

You can find the OpenAPI specs at /openapi, eg. https://meldportaal.acc.gkgi.nl/openapi.

## Authentication

The API endpoint is protected and you need an UZI server certificate to authenticate.

## Conditional fields

Some fields are only required when certain values are set in other fields.
Here is a list of these fields:

- `bco_number` is required when `test_after_contact_tracing` is set to `yes`.
- `country_stay` is required when `return_from_trip` is set to `yes`.
- `flight_number` is required when `return_from_trip` is set to `yes`.
- `first_day_of_illness_date` is required when `first_day_of_illness_known` is set to `known` or `estimated`.
- `date_of_notification_coronamelder_app` is required when `forwarded_by_coronamelder_app` is set to `yes`.
- `date_of_contact_coronamelder_app` is required when `forwarded_by_coronamelder_app` is set to `yes`.
- `caregiver_type` is required when `last_two_weeks_worked_as_at_in` contains `healthcare_worker_or_paramedic_in_hospital`, `care_worker_or_paramedic_in_nursing_or_care_home` or `healthcare_worker_or_paramedic_elsewhere_with_close_contact`.
- `contact_profession` is required when `last_two_weeks_worked_as_at_in` contains `other_professions_with_close_contact`, `care_worker_or_paramedic_in_nursing_or_care_home` or `healthcare_worker_or_paramedic_elsewhere_with_close_contact`.
- `risk_group` is required when `patient_gp_client_vvt_or_risk_group` is set to `patient_client` or `risk_group`.


## Authentication in development mode

- Generate a self-signed certificate with the following command:

```bash
  ./bin/generate-dev-uzi-certs.sh ssl
```

This will generate uzi-server-cert.* files in the `ssl` directory.

- Add the following to your `meldportaal-uzi.conf` file:
    
```nginx
  SSLCACertificateFile /etc/apache2/ssl/uzi-server-cert.crt
```

This will make Apache trust the self-signed certificate after you restart Apache.

- Next, you need to add the certificate to your browser. This can be done by importing the uzi-server-cert.p12 file into your browser. In firefox, 
goto `Tools | Settings | Privacy & Security | View Certificates | Your Certificates | Import`. Other browsers should follow a similar process.

- Make sure you have created a API user (with the admin) with the serial number `1234ABCD` and the common name `Test User`.

- Goto `https://<url>/api/health`. This will ask for a certificate. Select the certificate you just imported and you should see a `200 OK` response with a health = true json message.

- You can now use the API endpoint with the certificate you just imported. For instance, with curl you can use the `--client-cert` and `--client-key` options to add your client certificate.


## Test body v1
```php
[
    {
        "initials": "J.",
        "insertion": "",
        "surname": "lastname",
        "street": "Clausplein",
        "house_number": "10",
        "house_letter": "",
        "house_number_addition": "",
        "house_number_designation": "",
        "postcode": "5611XP",
        "city": "Eindhoven",
        "gender": "MAN",
        "birthdate": "02-11-1991",
        "bsn": "999990032",
        "email": "yenlotest@email.com",
        "phone": "+31612345678",
        "report_permission_gp": "NEE",
        "requesting_physician": "Onbekend",
        "brand_used_test": "R",
        "involved_laboratory": "",
        "category_test_location": "Commerciële testlocatie",
        "involved_company": "SON",
        "test_after_contact_tracing": "Nee",
        "bco_number": "",
        "return_from_trip": "Nee",
        "country_stay": "",
        "flight_number": "",
        "current_symptoms": "Geen van deze",
        "first_day_of_illness_known": "Onbekend",
        "first_day_of_illness_date": "",
        "forwarded_by_coronamelder_app": "Nee",
        "date_of_notification_coronamelder_app": "",
        "date_of_contact_coronamelder_app": "",
        "last_two_weeks_worked_as_at_in": "",
        "caregiver_type": "",
        "contact_profession": "",
        "patient_gp_client_vvt_or_risk_group": "",
        "risk_group": "",
        "date_of_sample_collection": "2021-09-21 11:25:00",
        "date_of_test_result": "2021-09-22 09:58:00",
        "test_result": "POSITIEF",
        "user_id": ""
    },
     {
        "initials": "J.",
        "insertion": "",
        "surname": "lastname",
        "street": "Clausplein",
        "house_number": "10",
        "house_letter": "",
        "house_number_addition": "",
        "house_number_designation": "",
        "postcode": "5611XP",
        "city": "Eindhoven",
        "gender": "MAN",
        "birthdate": "02-11-1991",
        "bsn": "999990032",
        "email": "yenlotest@email.com",
        "phone": "+31612345678",
        "report_permission_gp": "NEE",
        "requesting_physician": "Onbekend",
        "brand_used_test": "R",
        "involved_laboratory": "",
        "category_test_location": "Commerciële testlocatie",
        "involved_company": "SON",
        "test_after_contact_tracing": "Nee",
        "bco_number": "",
        "return_from_trip": "Nee",
        "country_stay": "",
        "flight_number": "",
        "current_symptoms": "Geen van deze",
        "first_day_of_illness_known": "Onbekend",
        "first_day_of_illness_date": "",
        "forwarded_by_coronamelder_app": "Nee",
        "date_of_notification_coronamelder_app": "",
        "date_of_contact_coronamelder_app": "",
        "last_two_weeks_worked_as_at_in": "",
        "caregiver_type": "",
        "contact_profession": "",
        "patient_gp_client_vvt_or_risk_group": "",
        "risk_group": "",
        "date_of_sample_collection": "2021-09-21 11:25:00",
        "date_of_test_result": "2021-09-22 09:58:00",
        "test_result": "POSITIEF",
        "user_id": ""
    }
]
```

## Test body v2

```json
{
    "initials": "Voornaam",
    "insertion": "tus",
    "surname": "lastname",
    "postcode": "1234AB",
    "house_number": 66,
    "house_letter": "a",
    "street": "blastreet",
    "city": "Amsterdam",
    "country": "NL",
    "gender": "unspecified",
    "birthdate": "1997-05-01",
    "bsn": "999995844",
    "email": "email@example.nl",
    "phone": "0612345678",
    "report_permission_gp": "no",
    "requesting_physician": "Bob",
    "brand_used_test": "A",
    "involved_laboratory": "Bobys laboratorium",
    "category_test_location": "regional_ggd",
    "involved_company": "laborum non anim",
    "test_after_contact_tracing": "yes",
    "bco_number": "fugiat velit cillum",
    "return_from_trip": "no",
    "country_stay": 5742,
    "flight_number": "dolor proident Lorem voluptate",
    "current_symptoms": [
        "sore_throat",
        "nasal_cold"
    ],
    "first_day_of_illness_known": "unknown",
    "forwarded_by_coronamelder_app": "no",
    "last_two_weeks_worked_as_at_in": [
        "informal_caregiver",
        "secondary_education_incl_mbo"
    ],
    "caregiver_type": "audiologist_hearing_care_professional",
    "contact_profession": "trainer_sports_instructor",
    "patient_gp_client_vvt_or_risk_group": "no",
    "risk_group": [
        "heart_patient",
        "diabetes_mellitus"
    ],
    "date_of_sample_collection": "2023-02-26",
    "date_of_test_result": "2023-02-27",
    "test_result": "positive"
}
```
