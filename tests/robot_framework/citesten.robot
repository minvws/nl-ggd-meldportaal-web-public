* Settings *
Library     Browser
Library     Collections
Library     DebugLibrary
Library     OperatingSystem
Library     OTP
Library     PostgreSQLDB
Library     Process
Library     String
Library     RPA.PDF

Force Tags      CI
Suite Setup     MELDPORTAAL Suite Setup
Suite Teardown  MELDPORTAAL Suite Teardown

* Variables *
${base_url}   localhost:8000

# * Test Cases *
# vaccinatie met nederlands als land
#     Fill and send vaccination form

| * Test Case *                        | Keyword                   | ${last-name} |  ${country}  |  ${bsn}   | ${birthdate}  | ${type}                 |  ${test_after_contact_tracing} |  ${return_from_trip} | ${first_day_of_illness_known}  |  ${patient_gp_client_vvt_or_risk_group}
| first day of illnes known            | Fill and send vaccination | last-name    |  Nederland   | 999990032 | 1950-11-11    | Antigeentest AMP        | Nee                            |  Nee                 | Geschat                        |  Nee
| Nederland                            | Fill and send vaccination | last-name    |  Nederland   | 999990032 | 1950-11-11    | PCR-test                | Nee                            |  Nee                 | Onbekend                       |  Nee
| Duitsland                            | Fill and send vaccination | last-name    |  Duitsland   | 999990032 | 1950-11-11    | LAMP test               | Nee                            |  Nee                 | Onbekend                       |  Nee
| After contract tracing               | Fill and send vaccination | last-name    |  Nederland   | 999990032 | 1950-11-11    | Mondspoeling            |  Ja                            |  Nee                 | Onbekend                       |  Nee
| Return from trip                     | Fill and send vaccination | last-name    |  Nederland   | 999990032 | 1950-11-11    | Zelftest                | Nee                            |  Ja                  | Onbekend                       |  Nee
| patient_gp_client_vvt_or_risk_group  | Fill and send vaccination | last-name    |  Nederland   | 999990032 | 1950-11-11    | Antigeentest Abbot      | Nee                            |  Nee                 | Onbekend                       |  Risicogroep
| Geen bsn                             | Fill and send vaccination | Achternaam    |  Nederland  |           | 1970-05-01    | PCR-test                | Nee                            |  Nee                 | Onbekend                       |  Nee



# vaccinatie met vrouwelijk geslacht
# vaccinatie met verkeerde bsn
# vaccinatie met verkeerde geboortedatum
# vaccinatie met merk AntigeenTest AMP
# vaccinatie met binnen 14 dagen terug gekeerd
# vaccinatie met smaakverlies als klacht
# vaccinatie met onbekende eerste dag van ziekte
# vaccinatie met persoon die gewerkt heeft bij voedselverwerking de laatste 2 weken
# vaccinatie met persoon die behoort tot risicogroep
# vaccinatie met afgenomen data 4 weken in verleden


* Keywords *
Fill and send vaccination
    [Arguments]  ${last-name}=last-name    ${country}=Nederland    ${bsn}=999990032    ${birthdate}=1950-11-11    ${type}=PCR-test
    ...          ${test_after_contact_tracing}=Nee    ${return_from_trip}=Nee    ${first_day_of_illness_known}=Geschat
    ...          ${patient_gp_client_vvt_or_risk_group}=Risicogroep
    Go To        ${base_url}
    Fill text  id=initials                          John
    Fill text  id=insertion                         do
    Fill text  id=surname                           ${last-name}
    Fill text  id=postcode                          1234AB
    Fill text  id=house_number                      74
    Fill text  id=house_letter                      a
    Fill text  id=house_number_addition             2
    Fill text  id=house_number_designation          rood
    Fill text  id=street                            blastreet
    Fill text  id=city                              Amsterdam
    Select Options By  id=country                   text          ${country}
    Select Options By  id=gender                    text          Man
    Fill text  id=bsn                               ${bsn}
    Fill text  id=birthdate                         ${birthdate}
    Fill text  id=email                             test@email.nl
    Fill text  id=phone                             0612345678
    Select Options By     id=report_permission_gp   text          Ja
    Fill text  id=requesting_physician              John
    Select Options By     id=brand_used_test        text          ${type}   # Verschillende opties kiezen die belangrijk zijn, 1 van elk type
    Fill text  id=involved_laboratory               lab100
    Select Options By     id=category_test_location  text          Asielzoekerscentrum
    Fill text  id=involved_company                  Company100
    Select Options By     id=test_after_contact_tracing   text          ${test_after_contact_tracing}
        IF  "${test_after_contact_tracing}"=="Ja"
            Fill text    id=bco_number    12345
        END
    Select Options By     id=return_from_trip             text          ${return_from_trip}
        IF  "${return_from_trip}"=="Ja"
            Select Options By     id=country_stay   text          Duitsland
            Fill text    id=flight_number    KLM12345
        END
    Click                 id=fever_or_chills
    Select Options By     id=first_day_of_illness_known   text          ${first_day_of_illness_known}
        IF    ("${first_day_of_illness_known}" == "Bekend") or "${first_day_of_illness_known}" == "Geschat"
            Type Text       id=first_day_of_illness_date          01-02-2023
        END
    # Select Options By     id=forwarded_by_coronamelder_app             text          Nee    # should be removed https://github.com/minvws/nl-ggd-meldportaal-web/issues/116
    Click                 id=animals_or_with_animal_products
    Select Options By     id=patient_gp_client_vvt_or_risk_group       text          ${patient_gp_client_vvt_or_risk_group}
        IF  ("${patient_gp_client_vvt_or_risk_group}" == "patient_client") or "${patient_gp_client_vvt_or_risk_group}" == "Risicogroep"
            Click    id=70_years_and_older
        END
    Type Text       id=date_of_sample_collection    01-02-2022
    Type Text       id=date_of_test_result          01-02-2022
    Select Options By     id=test_result       text          Positief
    Take Screenshot   fullPage=True
    Click                 "Bewaar test gegevens"
    Take Screenshot   fullPage=True
    Click                 id=submitForm
    Get Text   //body  *=  Test resultaat is successvol ingediend      Testresultaat ingediend niet zichtbaar
    Get Text   //body  *=  Nieuw test resultaat indienen               Nieuw test resultaat indienen button niet zichtbaar



MELDPORTAAL Suite Setup
    Create MELDPORTAAL Admin User
    # Connect To Database
    Login With Admin

Create MELDPORTAAL Admin User
    ${migrate_command}  Set Variable  php artisan migrate:fresh --seed
    Run Process      ${migrate_command}  shell=True  alias=migrate
    ${stdout}        ${stderr}      Get Process Result  migrate  stdout=True  stderr=True
    Should Be Empty  ${stderr}      Error migrating database: ${stderr}
    ${command}       Set Variable   php artisan user:create 'admin@email.com' 'admin' 'admin'
    Run Process      ${command}     shell=True  alias=create_admin
    ${stdout}        ${stderr}      Get Process Result  create_admin  stdout=True  stderr=True
    Should Be Empty  ${stderr}      Error creating admin: ${stderr}
    ${otp_code}      Get Regexp Matches  ${stdout}  [A-Z0-9]{16}
    Should Not Be Empty  ${otp_code}  No otp code found in: ${stdout}
    Set Suite Variable   ${otp_code}  ${otp_code[0]}

Login With Admin
    New Browser     firefox    headless=${headless}     args=["--ignore-certificate-errors"]
    New Page    ${base_url}
    Fill Text   id=email        admin@email.com
    Fill Text   id=password     admin
    Click       " Inloggen "
    ${otp}      get otp         ${otp_code}
    Fill Text   id=code         ${otp}

    Click       " Inloggen "

MELDPORTAAL Suite Teardown
    ${BROWSER_LOGS}     Close Page
    Close Browser
    Log         ${BROWSER_LOGS}
    ${LOG_FILE}  List Directory  storage/logs   *.log  True
    IF  ${LOG_FILE}
        ${LARAVEL_LOGS}     Get File    storage/logs/laravel.log
        Log     ${LARAVEL_LOGS}
        Copy File           storage/logs/laravel.log    ${OUTPUT DIR}
    END

Connect To Database
    ${ENV_FILE}         Get File    .env
    ${DB_PORT_LINE}     Get Lines Containing String  ${ENV_FILE}  DB_PORT
    ${DB_HOST_LINE}     Get Lines Containing String  ${ENV_FILE}  DB_HOST
    # Connect To Postgresql   ci_db_test  postgres  postgres  ${DB_HOST_LINE.split("=")[1]}  dbport=${DB_PORT_LINE.split("=")[1]}
    Connect To Postgresql   viep_db  postgres  postgres  ${DB_HOST_LINE.split("=")[1]}  dbport=${DB_PORT_LINE.split("=")[1]}
