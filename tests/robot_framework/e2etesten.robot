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

Force Tags      E2E
Suite Setup     MELDPORTAAL Suite Setup
Suite Teardown  MELDPORTAAL Suite Teardown

* Variables *
${base_url}   https://meldportaal.acc.gkgi.nl/
${username}   robot@example.com     
${password}   MushilyDiffusionCaptureEarring 
${otp_secret}    %{OTP_SECRET_MELDPORTAAL}

# Usable TestData for monster check: lastname, birthdate, bsn: https://www.rvig.nl/brp/documenten/richtlijnen/2018/09/20/testdataset-persoonslijsten-proefomgevingen-gba-v
| * Test Case *                        | Keyword                   | ${last-name}  | ${postal-code} |  ${country}  |  ${bsn}   | ${birthdate}  | ${type}                 |  ${test_after_contact_tracing} |  ${return_from_trip} | ${first_day_of_illness_known}  |  ${patient_gp_client_vvt_or_risk_group}
# | Nederland                            | Fill and send vaccination | Moulin        |  1012AB        |  Nederland   | 999993653 | 1985-12-01    | PCR-test                | Nee                            |  Nee                 | Onbekend                       |  Nee
| Duitsland en duits adress            | Fill and send vaccination | Moulin        |  52062         |  Duitsland   | 999993653 | 1985-12-01    | LAMP test               | Nee                            |  Nee                 | Onbekend                       |  Nee
# | After contract tracing               | Fill and send vaccination | Moulin        |  1012AB        |  Nederland   | 999993653 | 1985-12-01    | Mondspoeling            | Ja                             |  Nee                 | Onbekend                       |  Nee
# | Return from trip                     | Fill and send vaccination | Moulin        |  1012AB        |  Nederland   | 999993653 | 1985-12-01    | Zelftest                | Nee                            |  Ja                  | Onbekend                       |  Nee
# | first day of illnes known            | Fill and send vaccination | Moulin        |  1012AB        |  Nederland   | 999993653 | 1985-12-01    | Antigeentest AMP        | Nee                            |  Nee                 | Geschat                        |  Nee
# | patient_gp_client_vvt_or_risk_group  | Fill and send vaccination | Moulin        |  1012AB        |  Nederland   | 999993653 | 1985-12-01    | Antigeentest Abbot      | Nee                            |  Nee                 | Onbekend                       |  Risicogroep
# | Geen bsn                             | Fill and send vaccination | Achternaam    |  1012AB        |  Nederland   |           | 1970-05-01    | PCR-test                | Nee                            |  Nee                 | Onbekend                       |  Nee       
# | Wrong bsn gives error                | Fill and send vaccination | Achternaam    |  1012AB        |  Nederland   | 000000000 | 1970-05-01    | PCR-test                | Nee                            |  Nee                 | Onbekend                       |  Nee       
# | Wrong birthdate gives error          | Fill and send vaccination | Achternaam    |  1012AB        |  Nederland   | 999993653 | 05-01-1970    | PCR-test                | Nee                            |  Nee                 | Onbekend                       |  Nee       



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
    [Arguments]  ${last-name}=last-name    ${postal-code}=    ${country}=Nederland    ${bsn}=     ${birthdate}=1950-11-11    ${type}=PCR-test
    ...          ${test_after_contact_tracing}=Nee    ${return_from_trip}=Nee    ${first_day_of_illness_known}=Geschat
    ...          ${patient_gp_client_vvt_or_risk_group}=Risicogroep
    Go To        ${base_url}
    Fill text  id=initials                          Corrie
    Fill text  id=insertion                         Van
    Fill text  id=surname                           ${last-name}
    Fill text  id=postcode                          ${postal-code}
    Fill text  id=house_number                      19
    Fill text  id=house_letter                      a
    # Fill text  id=house_number_addition             
    Fill text  id=house_number_designation          0612345678
    Fill text  id=street                            straatnaam 
    Fill text  id=city                              Woonplaats   
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
            Type Text       id=first_day_of_illness_date          10-02-2023
        END
    # Select Options By     id=forwarded_by_coronamelder_app             text          Nee    # should be removed https://github.com/minvws/nl-ggd-meldportaal-web/issues/116
    Click                 id=animals_or_with_animal_products
    Select Options By     id=patient_gp_client_vvt_or_risk_group       text          ${patient_gp_client_vvt_or_risk_group}
        IF  ("${patient_gp_client_vvt_or_risk_group}" == "patient_client") or "${patient_gp_client_vvt_or_risk_group}" == "Risicogroep"
            Click    id=70_years_and_older
        END    
    Type Text       id=date_of_sample_collection    08-08-2022
    Type Text       id=date_of_test_result          08-08-2022     
    Select Options By     id=test_result       text          Positief
    debug
    Click                 "Bewaar test gegevens"
        IF    "${bsn}"=="000000000"     
            Get Text   //body  *=  Foutmelding                       foutmelding bovenaan de pagina niet zichtbaar
            Get Text   //body  *=  BSN does not exists               bsn error niet zichtbaar
        ELSE IF    "${birthdate}"=="05-01-1970"
            Get Text   //body  *=  Foutmelding                       foutmelding bovenaan de pagina niet zichtbaar
            Get Text   //body  *=  Failed to match bsn with birth date               monster check zou error moeten geven
            Get Text   //body  *=  Het formaat van Geboortedatum is ongeldig.        verkeerde format van geboortedatum zou error moeten geven
        ELSE
            Click    id=submitForm
            Get Text   //body  *=  Test resultaat is successvol ingediend      Testresultaat ingediend niet zichtbaar
            Get Text   //body  *=  Nieuw test resultaat indienen               Nieuw test resultaat indienen button niet zichtbaar
        END    
    Sleep  3s

MELDPORTAAL Suite Setup
    Login With Admin

Login With Admin
    New Browser     chromium    headless=${headless}     args=["--ignore-certificate-errors"]
    New Page    ${base_url}
    Fill Text   id=email        robot@example.com
    Fill Text   id=password     MushilyDiffusionCaptureEarring
    Click       " Inloggen "
    ${otp}      get otp         ${otp_secret}
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
