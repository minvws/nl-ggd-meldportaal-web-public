### Test types
The Robot Framework tests are mainly frontend (browser) tests, but are also use to:
- Call API endpoints and assert the response
- Query Postgres databases and assert values (rows/columns)
- Read pdf's (convert to text) and assert values
- Read QR codes
- Unzip files
- Read .cvs files (for example as test input data)

The tests are divided in 2 types, CI and E2E.

**CI tests**
Are used to build the frontend code, connect to a local PostgreSQL database and run the frontend functionality in a isolated state (de backend dependencies). The CI tests run on with every pull request and are mandatory for merging.

**E2E tests**
Are used to run on acceptance environment and thus use the "real" backends and databases. There tests can only be run manually from git Github actions. This workflow (pipeline) is called: `E2E regression tests`.

### Prerequisites:
- Python > 3 and < 3.9
- Build the FE code:
  - `composer install` for installing php dependencies
  - `npm run build` for building the frontend code
- `php artisan serve` for running the frontend on `http://localhost:8000`
- Install postgreSQL, visit: `https://www.postgresql.org/download/`
- Install RabbitMQ, visit: `https://www.rabbitmq.com/download.html`
- Install Redis, visit: `https://redis.io/docs/getting-started/`
- Add database to your local postgreSQL (see .env for database name)
- Cleaning en seeding the database table is done automatically by Robot Framework.

### Installation
What we are installing:
- Robot Framework
- Robot Framework Browser (for browser automation)
- Robot Framework Archive (for unpacking .zips)
- Robot Framework DebugLibrary (for pausing tests in between steps)
- Robot Framework OTP (for handling 2FA codes)
- Robot Framework PDF (for reading text from .pdfs)
- qrcode (python library for reading QR code images)

To install the Robot Framework dependencies, first run:
`make install-rf`


### Run tests local
Start the tests locally:
Use `make test-rf` to run all tests (headless).
Use `make test-rf/<tag>` to run only tests with given tag (headless).
Use `make test-rf-head` to run only tests with given tag (with browser open for debugging).

In the pipeline we use: `make test-rf/ci` to run all tests tagged `ci`.
