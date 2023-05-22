# Logging

We use the following events in meldportaal:

| Event                           | `event_code` (string) | Routing key (prefixed with `[app].[env].`[^1]) |
|---------------------------------|-----------------------|------------------------------------------------|
| Sign in                         | `091111`              | `user_login`                                   |
| Failed sign in                  | `091111`              | `user_login`                                   |
| Sign out                        | `092222`              | `user_logout`                                  |
| TwoFactor authentication failed | `093333`              | `user_login_two_factor_failed`                 |
| Report test                     | `090631`              | `report`                                       |
| BSN lookup                      | `080401`              | `bsn_lookup`                                   |


# Event details

### Sign in (091111)
Triggered when a user signs in with the correct email/password combination and valid OTP code.

```json
{
  "user_id": 1,
  "request": {
    "user_id": 1,
    "user_email": "user@example.org",
    "user_roles": [
      "USER"
    ]
  },
  "created_at": "2023-02-10T09:51:01.486172Z",
  "event_code": "091111",
  "action_code": "E",
  "allowed_admin_view": false,
  "failed": false,
  "failed_reason": null,
  "email": "user@example.org"
}
```

### Failed sign in (091111)

Triggered when either the email address or password is incorrect. The `failed_reason` field will be set to `invalid_email` or `invalid_password` respectively.

```json
{
  "user_id": null,
  "request": {
    "user_id": null,
    "user_roles": null,
    "user_email": "notexists@example.org",
    "partial_password_hash": "b37ac2763ce1c700"
  },
  "created_at": "2023-02-10T09:46:06.283288Z",
  "event_code": "091111",
  "action_code": "E",
  "allowed_admin_view": false,
  "failed": true,
  "failed_reason": "invalid_email",
  "email": null
}
```

### Sign out (092222)

Triggered when a user signs out.

```json
{
  "user_id": 1,
  "request": [],
  "created_at": "2023-02-10T09:53:00.226445Z",
  "event_code": "092222",
  "action_code": "E",
  "allowed_admin_view": false,
  "failed": false,
  "failed_reason": null,
  "email": "user@example.org"
}
```

### Two factor authentication failed (093333)

Triggered when the two factor authentication code is incorrect. Note that when this event is triggered, the user has supplied correct 
email/password combination, but the sign-in (091111) event is ONLY triggered when the OTP code is also correct.

```json
{
  "user_id": 1,
  "request": [],
  "created_at": "2023-02-10T09:48:58.985836Z",
  "event_code": "093333",
  "action_code": "E",
  "allowed_admin_view": false,
  "failed": false,
  "failed_reason": null,
  "email": "user@example.org"
}
```

### Report test (090631)

Triggered when a new test report has been successfully created

```json
{
  "user_id": 1,
  "request": {
    "data": {
      "initials": "J",
      "insertion": "",
      "surname": "last-name",
      "postcode": "1234ab",
      "house_number": "123",
      "house_letter": "",
      "house_number_addition": "",
      "house_number_designation": "",
      "street": "Burgemeester Jhr. Quarles van Uffordlaan",
      "city": "Apeldoorn",
      "country": "NL",
      "gender": "unspecified",
      "bsn": "149361117",
      "birthdate": "2001-01-01",
      "email": "john@deadcode.nl",
      "phone": "0611111116",
      "report_permission_gp": "yes",
      "requesting_physician": "Dr Bernard",
      "brand_used_test": "C",
      "involved_laboratory": "The lab",
      "category_test_location": "education",
      "involved_company": "Medisch Centrum West",
      "test_after_contact_tracing": "no",
      "bco_number": "",
      "return_from_trip": "yes",
      "country_stay": "AU",
      "flight_number": "KLM1234",
      "current_symptoms": [
        "sore_throat",
        "severe_muscle_pain"
      ],
      "first_day_of_illness_known": "estimated",
      "first_day_of_illness_date": "2023-02-01",
      "forwarded_by_coronamelder_app": "doesnt_use_the_app",
      "date_of_notification_coronamelder_app": "",
      "date_of_contact_coronamelder_app": "",
      "last_two_weeks_worked_as_at_in": [
        "healthcare_worker_or_paramedic_in_hospital",
        "primary_school_and_after_school_care_4_12"
      ],
      "caregiver_type": "carer",
      "contact_profession": "",
      "patient_gp_client_vvt_or_risk_group": "patient_client",
      "risk_group": [
        "airway_or_lung_problems",
        "diabetes_mellitus",
        "dementia",
        "parkinsons"
      ],
      "date_of_sample_collection": "2023-01-29",
      "date_of_test_result": "2023-01-30",
      "test_result": "positive",
      "brp_first_names": "first-name",
      "brp_prefix_surname": "prefix-surname",
      "brp_surname": "last-name",
      "brp_date_of_birth": "19870401",
      "eu_event_type": "LP6464-4",
      "eu_event_manufacturer": null,
      "eu_event_name": "SARS-CoV-2 Polymerase Chain Reaction (PCR)"
    },
    "id": "f3a242f9-f751-4ada-ad9c-6c4f4fa492b3"
  },
  "created_at": "2023-02-10T11:16:25.440854Z",
  "event_code": "090631",
  "action_code": "C",
  "allowed_admin_view": false,
  "failed": false,
  "failed_reason": null,
  "email": "user@example.org"
}
```

### BSN lookup (080401)

Triggered when a BSN lookup has been performed

```json
{
  "user_id": 1,
  "request": {
    "success": true,
    "bsn": "bXC5WnvV9gFSYcnjMg79ml7DvgKy1BJhTV2JyfxLuSYOnCSwa3zqaasBLRscx6ykaC8aoI+ueq7x",
    "date_of_birth": "1MrBI5BjF9RE8zFWb7mNBeVKyWil0C0WrlOhDY68XxWUklHwDIZ9ncn840bSt8RntlaG7LID"
  },
  "created_at": "2023-02-10T10:00:54.766461Z",
  "event_code": "080401",
  "action_code": "E",
  "allowed_admin_view": false,
  "failed": false,
  "failed_reason": null,
  "email": "user@example.org"
} 
```
