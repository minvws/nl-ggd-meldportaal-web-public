# Sync with [INGE 7](https://github.com/minvws/nl-covid19-registration-coronacheck-api)

The Meldportaal saves a 'test' model asymmetric encrypted in the database. The sync script will decrypt the test model, mapping it to a [positive test event](https://github.com/minvws/nl-covid19-coronacheck-provider-docs/blob/main/docs/data-structures-overview.md#positive-test-event) and saves the event in the [INGE 7](https://github.com/minvws/nl-covid19-registration-coronacheck-api) Redis DB.

## DB Encryption

We use [Sodium sealed boxes](https://doc.libsodium.org/public-key_cryptography/sealed_boxes) for the encryption of the
test model. We will setup the application with the public key of the sync private key. The sync will be able to decrypt
the test model data with its private key.

For decrypting the database `DATABASE_PUBLIC_KEY` and `DATABASE_PRIVATE_KEY` environment variables are needed.

## Connection to Redis

The connection to Redis can be configured through the following environment variables:

| Environment Variable             | Description | Default   |
|----------------------------------|-------------|-----------|
| INGE7_REDIS_SCHEME               |             | tcp       |
| INGE7_REDIS_URL                  |             |           |
| INGE7_REDIS_HOST                 |             | 127.0.0.1 |
| INGE7_REDIS_PASSWORD             |             |           |
| INGE7_REDIS_PORT                 |             | 6379      |
| INGE7_REDIS_DB                   |             | 0         |
| INGE7_REDIS_PREFIX               |             |           |
| INGE7_REDIS_TLS_PEER_NAME        |             |           |
| INGE7_REDIS_TLS_VERIFY_PEER      |             | true      |
| INGE7_REDIS_TLS_VERIFY_PEER_NAME |             | true      |
| INGE7_REDIS_TLS_CAFILE           |             |           |
| INGE7_REDIS_TLS_LOCAL_CERT       |             |           |
| INGE7_REDIS_TLS_LOCAL_PK         |             |           |

Before we store the holder information and the positive test event in Redis, we encrypt the data with the public key of Inge 7. We use Sodium Sealed Boxes for this.
You can configure the public key with the `INGE7_PUBKEY` environment variable.

## Identity Hash

Inge 7 loads the data from Redis with the identity hash as key. We need to save the holder information and events with the same identity hash.

For this you need to configure the `INGE7_IDENTITY_HASH_SECRET` environment variable.

## Mapping test model to a positive test event

We need to map the internal test model to
a [positive test event](https://github.com/minvws/nl-covid19-coronacheck-provider-docs/blob/main/docs/data-structures-overview.md#positive-test-event).
The positive test event exists of a holder part and an event part.

Not every 'test' can directly be used to create a positive test event. For this we created
a [mapping table](#mapping-table-of-used-test-brands).

### Holder part

Example:

```json
{
    "protocolVersion": "3.0",
    "providerIdentifier": "XXX",
    "status": "complete",
    "holder": {
        "bsn": "999999999",
        "firstName": "Bob",
        "infix": "de",
        "lastName": "Bouwer",
        "birthDate": "YYYY-MM-DD"
    }
}
```

To configure the providerIdentifier you can set the `INGE7_PROVIDER_IDENTIFIER` environment variable.

The firstName, infix, lastName and birthDate values are from [monster](https://github.com/minvws/nl-rdo-bsn-api), we
enrich the test model with this information when the test is created.

### Event part

Example:

```json
{
    "type": "positivetest",
    "unique": "66d7745c-445d-4440-a50e-e23afa73275e",
    "isSpecimen": true,
    "positivetest": {
        "sampleDate": "2021-01-01T00:00:00Z",
        "positiveResult": true,
        "facility": "Some facility",
        "type": "LP6464-4",
        "name": "SARS-CoV-2 Polymerase Chain Reaction (PCR)",
        "manufacturer": "",
        "country": "NL"
    }
}
```

### Mapping table of used test brands

We have created a mapping based on the GGD / Yenlo values that you currently can select in the application.
For this we have asked what the current mapping is on their site and we discussed that
in [this issue](https://github.com/minvws/nl-ggd-meldportaal-web/issues/97).

When a value of `brand_used_test` is `Z` or not in the mapping list, than it will be mapped to `null` and means that the
test will not be synced to INGE 7.

| Brand option                       | Value of `brand_used_test` | Mapped type | Mapped manufacturer | Mapped name                                              |
|------------------------------------|----------------------------|-------------|---------------------|----------------------------------------------------------|
| PCR-test                           | C                          | LP6464-4    |                     | SARS-CoV-2 Polymerase Chain Reaction (PCR)               |
| LAMP test                          | L                          | LP6464-4    |                     | SARS-CoV-2 Loop-Mediated Isothermal Amplification (LAMP) |
| Mondspoeling                       | M                          | LP6464-4    |                     | SARS-CoV-2 Polymerase Chain Reaction (PCR)               |
| Zelftest                           | Z                          | -           | -                   | -                                                        |
| Antigeentest Abbot                 | A                          | LP217198-3  | 1232                | Panbio COVID-19 Ag Test                                  |
| Antigeentest AMP                   | T                          | LP217198-3  |                     |                                                          |
| Antigeentest BD (Becton Dickinson) | B                          | LP217198-3  | 1065                | BD Veritor System for Rapid Detection of SARS-CoV-2      |
| Antigeentest Biosynex              | E                          | LP217198-3  |                     |                                                          |
| Antigeentest Biozek                | F                          | LP217198-3  |                     |                                                          |
| Antigeentest Boditech              | G                          | LP217198-3  |                     |                                                          |
| Antigeentest DiaSorin              | D                          | LP217198-3  | 1960                | LIAISON\u00ae SARS-CoV-2 Ag                              |
| Antigeentest Diano                 | J                          | LP217198-3  |                     |                                                          |
| Antigeentest Healgen               | H                          | LP217198-3  |                     |                                                          |
| Antigeentest LumiraDX              | K                          | LP217198-3  |                     |                                                          |
| Antigeentest Meridian Bioscience   | N                          | LP217198-3  | 1244                | GenBody COVID-19 Ag                                      |
| Antigeentest Quidel                | Q                          | LP217198-3  | 1097                | Sofia SARS Antigen FIA                                   |
| Antigeentest Roche                 | R                          | LP217198-3  | 345                 | STANDARD Q COVID-19 Ag Test                              |
| Antigeentest Romed                 | P                          | LP217198-3  |                     |                                                          |
| Antigeentest SD Biosensor          | S                          | LP217198-3  | 344                 | STANDARD F COVID-19 Ag FIA                               |
| Antigeentest Siemens Healthineers  | U                          | LP217198-3  |                     |                                                          |
| Antigeentest Wantai                | W                          | LP217198-3  |                     |                                                          |
