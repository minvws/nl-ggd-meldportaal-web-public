# Meldportaal Positieve Tests

This repository contains a web form and API for reporting positive COVID-19 test results to the GGD Contact (BCO) and providing this data to the Corona Check app and its print site.

- **Physician Authentication**: Physicians can authenticate themselves using an [UZI card](https://www.uziregister.nl/uzi-pas).
  - Alternatively, they can log in with password and 2FA.
- **Data Validation**: Basic validation of the data entered is conducted to ensure its accuracy and completeness.
- **Data Verification**: When applicable, the patient's name and date of birth are verified against BRP using the [minvws/nl-rdo-bsn-api](https://github.com/minvws/nl-rdo-bsn-api).
- **Data Retrieval**: The tested individual can retrieve their data to generate a recovery proof in the Corona Check app or print portal using the [minvws/nl-covid19-registration-coronacheck-api](https://github.com/minvws/nl-covid19-registration-coronacheck-api).

## Dependencies

To run this project, you need the following tools installed on your system:

- [PHP 8](https://www.php.net/)
- [Composer](https://getcomposer.org/), PHP's dependency manager.
- [PostgreSQL](https://www.postgresql.org/), an open-source relational database management system.
- [Redis](https://redis.io/), an open-source in-memory database used for [minvws/nl-covid19-registration-coronacheck-api](https://github.com/minvws/nl-covid19-registration-coronacheck-api).

## Installation & Running Locally

**Note**: As an alternative to the steps below, you can use `docker compose` to manage dependencies and other setup tasks.

To install and run the Meldportaal Positive Tests Portal locally, follow these steps:

1. Initialize Meldportaal:
```sh
make init-meldportaal
```

2. Open the `.env` file in your editor of choice and add your database configuration. Make sure your PostgreSQL database is running and accessible.

3. Install the development environment:
```sh
make install-dev
```

4. Generate an admin user and display the corresponding login credentials.
```sh
make add-user
```

5. Run the local development server:
```sh
make serve-dev
```

## Testing

For information on how to run our Robot Framework frontend tests, please refer to our [end-to-end testing guide](./documentation/e2etests.md).
