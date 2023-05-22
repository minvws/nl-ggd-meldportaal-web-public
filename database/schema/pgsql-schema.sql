-- Wie : Herman
-- Waarom : init
-- Datum : 8-2-2023

do $$
<<first_block>>
declare
  ln_count integer := 0;
begin
   -- Check if user exists

   select count(*)
   into ln_count
   from pg_user
   where usename = 'kes';

   if ln_count = 0 then
     CREATE user kes password 'kes';
   end if;

   select count(*)
   into ln_count
   from pg_roles
   where rolname = 'meldportaal_dba';

   if ln_count = 0 then
     CREATE role meldportaal_dba ;
   end if;


end first_block $$;


GRANT CONNECT ON DATABASE meldportaal_db to kes;

-- Wie : Herman
-- Waarom : init
-- Datum : 8-2-2023



create sequence users_id_seq;

alter sequence users_id_seq owner to meldportaal_dba;

create table mp_sessions
(
    id            varchar(255) not null
        primary key,
    user_id       bigint,
    ip_address    varchar(45),
    user_agent    text,
    payload       text         not null,
    last_activity integer      not null
);

alter table mp_sessions
    owner to meldportaal_dba;

create table mp_users
(
    id                        bigint  default nextval('users_id_seq'::regclass) not null
        constraint users_pkey
            primary key,
    name                      varchar(255)                                      not null,
    email                     varchar(255)                                      not null
        constraint users_email_unique
            unique,
    created_at                timestamp(0),
    updated_at                timestamp(0),
    email_verified_at         timestamp(0),
    password_updated_at       timestamp(0),
    password                  varchar(255),
    remember_token            varchar(100),
    two_factor_secret         text,
    two_factor_recovery_codes text,
    roles                     json,
    active                    boolean default true                              not null,
    uzi_number                varchar(30),
    uuid                      uuid
        constraint users_uuid_key
            unique,
    terms_accepted_at         timestamp(0),
    terms_accepted            integer,
    last_login_at             timestamp(0),
    last_active_at            timestamp(0)
);

alter table mp_users
    owner to meldportaal_dba;

alter sequence users_id_seq owned by mp_users.id;

create table login_activity
(
    user_id      bigint,
    ip_address   varchar(45),
    logged_in_at timestamp(0),
    scope        varchar(50)
);

alter table login_activity
    owner to meldportaal_dba;


create table mp_tests
(
    initials                              text not null,
    insertion                             text,
    surname                               text not null,
    postcode                              text not null,
    house_number                          text not null,
    house_letter                          text,
    house_number_addition                 text,
    house_number_designation              text,
    street                                text not null,
    city                                  text not null,
    gender                                text not null,
    bsn                                   text not null,
    birthdate                             text not null,
    email                                 text not null,
    phone                                 text not null,
    report_permission_gp                  text,
    requesting_physician                  text,
    brand_used_test                       text,
    involved_laboratory                   text,
    category_test_location                text,
    involved_company                      text,
    test_after_contact_tracing            text,
    bco_number                            text,
    return_from_trip                      text,
    country_stay                          text,
    flight_number                         text,
    current_symptoms                      text,
    first_day_of_illness_known            text,
    first_day_of_illness_date             text,
    forwarded_by_coronamelder_app         text,
    last_two_weeks_worked_as_at_in        text,
    date_of_notification_coronamelder_app text,
    date_of_contact_coronamelder_app      text,
    caregiver_type                        text,
    contact_profession                    text,
    patient_gd_client_vvt_or_risk_group   text,
    risk_group                            text,
    date_of_sample_collection             date not null,
    date_of_test_result                   date not null,
    test_result                           text not null,
    updated_at                            timestamp(0),
    created_at                            timestamp(0),
    id                                    uuid not null
        constraint id
            primary key
);

alter table mp_tests
    owner to meldportaal_dba;


alter table mp_tests
    add synced bool default false;

alter table mp_tests
    add synced_at timestamp(0);
-- Wie : Herman
-- Waarom : init
-- Datum : 8-2-2023

alter table mp_sessions owner to meldportaal_dba;

alter table mp_users owner to meldportaal_dba;

alter table login_activity owner to meldportaal_dba;

alter table mp_tests owner to meldportaal_dba;


-- Wie : Herman
-- Waarom : init
-- Datum : 8-2-2023


GRANT CONNECT ON DATABASE meldportaal_db to kes;

GRANT ALL ON SEQUENCE users_id_seq TO kes;

-- Wie : Herman
-- Waarom : init
-- Datum : 8-2-2023

CREATE TABLE public.deploy_releases (
    version character varying(255),
    deployed_at timestamp without time zone DEFAULT now()
);

alter table deploy_releases owner to meldportaal_dba;

grant select  on deploy_releases to kes;
-- Wie : Herman
-- Waarom : init
-- Datum : 8-2-2023

grant insert on login_activity to kes;
grant select,insert,update,delete on mp_sessions to kes;
grant select,insert,update,delete on mp_tests to kes;
grant select,insert,update,delete on mp_users to kes;

-- Wie : Herman
-- Waarom : init
-- Datum : 8-2-2023

alter table mp_tests
    rename column synced to i7_synced;

alter table mp_tests
    rename column synced_at to i7_synced_at;


alter table mp_tests
    add ggd_synced bool default false;

alter table mp_tests
    add ggd_synced_at timestamp(0);

alter table mp_tests
    add country text;

alter table mp_tests
    rename column patient_gd_client_vvt_or_risk_group to patient_gp_client_vvt_or_risk_group;
-- Wie : Herman
-- Waarom : init
-- Datum : 8-2-2023


alter table mp_tests
    rename column ggd_synced to ggd_synchronised;

alter table mp_tests
    rename column ggd_synced_at to ggd_synchronised_at;

alter table mp_tests
    rename column i7_synced to i7_synchronised;

alter table mp_tests
    rename column i7_synced_at to i7_synchronised_at;
-- Wie : Herman
-- Waarom : init
-- Datum : 8-2-2023


alter table mp_tests
    add brp_first_names text;

alter table mp_tests
    add brp_prefix_surname text;

alter table mp_tests
    add brp_surname text;

alter table mp_tests
    add brp_date_of_birth text;
-- Wie : Anne Jan
-- Waarom : init
-- Datum : 15-2-2023

alter table mp_tests
    add eu_event_type text;

alter table mp_tests
    add eu_event_manufacturer text;

alter table mp_tests
    add eu_event_name text;
-- Wie : Joshua Thijssen
-- Waarom : initial tables for admin panel
-- Datum : 17-2-2023

alter table mp_users
    add created_by bigint;

alter table mp_users
    ADD CONSTRAINT created_by_foreign FOREIGN KEY (created_by) REFERENCES mp_users(id);

create table mp_credentials
(
    id         uuid not null
        constraint mp_credentials_pkey
            primary key,
    twofa_url  text not null,
    password   text not null,
    user_id    bigint
        constraint mp_credentials_user_id_foreign
            references mp_users,
    created_at timestamp(0),
    updated_at timestamp(0)
);


alter table mp_users
    add downloaded_at timestamp;

alter table mp_credentials owner to meldportaal_dba;

grant all on mp_credentials to kes;
-- Wie : Rob
-- Waarom : Recht zetten gebruiker, rename van kes naar void.
-- Datum : 17-2-2023

do $$
<<first_block>>
declare
  ln_count integer := 0;
begin
   -- Check if user exists

   select count(*)
   into ln_count
   from pg_user
   where usename = 'void';

   if ln_count = 0 then
     CREATE user void password 'void';
   end if;

end first_block $$;


GRANT INSERT ON TABLE public.login_activity TO void;

GRANT ALL ON TABLE public.mp_credentials TO void;

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.mp_sessions TO void;

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.mp_tests TO void;

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.mp_users TO void;

GRANT ALL ON SEQUENCE public.users_id_seq TO void;


REVOKE INSERT ON TABLE public.login_activity from kes;

REVOKE ALL ON TABLE public.mp_credentials from kes;

REVOKE SELECT,INSERT,DELETE,UPDATE ON TABLE public.mp_sessions from kes;

REVOKE SELECT,INSERT,DELETE,UPDATE ON TABLE public.mp_tests from kes;

REVOKE SELECT,INSERT,DELETE,UPDATE ON TABLE public.mp_users from kes;

REVOKE ALL ON SEQUENCE public.users_id_seq from kes;

GRANT CONNECT on database meldportaal_db to void;

REVOKE CONNECT on database meldportaal_db from kes;




-- Wie : Rob
-- Waarom : Nieuwe gebruiker apollo voor meldadmin (meldportaal).
-- Datum : 17-2-2023

do $$
<<first_block>>
declare
  ln_count integer := 0;
begin
   -- Check if user exists

   select count(*)
   into ln_count
   from pg_user
   where usename = 'apollo';

   if ln_count = 0 then
     CREATE user apollo password 'apollo';
   end if;

end first_block $$;



GRANT SELECT,INSERT,UPDATE ON TABLE public.mp_credentials TO apollo;

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.mp_users TO apollo;

GRANT ALL ON SEQUENCE public.users_id_seq TO apollo;


GRANT CONNECT on database meldportaal_db to apollo;





-- Wie : Joshua Thijssen
-- Waarom : add serial number for api users
-- Datum : 21-2-2023

alter table mp_users add uzi_serial text;
-- Wie : Rob
-- Waarom : Recht zetten gebruiker
-- Datum : 22-2-2023



GRANT SELECT ON TABLE public.deploy_releases TO void;

GRANT SELECT ON TABLE public.deploy_releases TO apollo;

-- Wie : Herman
-- Waarom : extra rechten
-- Datum : 22-2-2023

GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE public.mp_sessions TO apollo;
-- Wie : Joshua Thijssen
-- Waarom : add specimen column to mp_users
-- Datum : 23-2-2023

alter table mp_users add is_specimen bool default false;
-- Wie : Joshua Thijssen
-- Waarom : add specimen column to mp_tests and removing from mp_users
-- Datum : 24-2-2023

alter table mp_tests add is_specimen bool default false;

alter table mp_users drop column is_specimen;
-- Wie : Joshua Thijssen
-- Waarom : change date to timestamp
-- Datum : 21-3-2023

alter table public.mp_tests
    alter column date_of_sample_collection type timestamp(0) using date_of_sample_collection::timestamp(0);

