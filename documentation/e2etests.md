## Running Robot Framework frontend tests

### Installation
To install the Robot Framework dependencies, first run:
`make install-rf`

### Test types
The Robot Framework tests are used for frontend (browser) tests in this repository.
The tests are divided in 2 types, CI and E2E.

**CI tests**
Are used to build the frontend code and run the frontend functionality in an isolated state (using mocks to cover backend dependencies). The CI tests run on with every pull request and are mandatory for merging.
We can also run the e2e test locally with the make command `make test-rf-head/ci`

**E2E tests**
Are used to run on acceptance environment and thus use the "real" backends and databases. 
We can also run the e2e test locally with the make command `make test-rf-head/e2e`
To run against the ACC environment you need to set your credentials in [e2etesten.robot](./../robot_framework/e2etesten.robot)


