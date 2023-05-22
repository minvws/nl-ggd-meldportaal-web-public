<?php

declare(strict_types=1);

namespace Tests\Helper;

use MinVWS\PUZI\UziConstants;

class TestUziCertificateGenerator
{
    public function generateCertificate(string $uziNumber): array
    {
        $subject = [
            'countryName' => "NL",
            'organizationName' => 'MockTest Cert',
            'commonName' => 'test.example.org',
            'serialNumber' => $uziNumber,
        ];

        // Create private key
        $privkey = openssl_pkey_new([
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ]);

        // Generate a certificate signing request
        $csr = openssl_csr_new($subject, $privkey, ['digest_alg' => 'sha256',]);
        if ($csr === false) {
            throw new \RuntimeException("Could not generate certificate: " . implode(", ", $this->getOpenSslErrors()));
        }

        // Create config file to set subjectAltName for openssl_csr_sign
        $configFile = tmpfile();
        $configFilePath = stream_get_meta_data($configFile)['uri'];

        $subjectAltName = $this->buildSubjectAltName($uziNumber);
        $this->writeOpenSslConfig($configFile, $subjectAltName);

        // Create self-signed certificate with subjectAltName
        $x509 = openssl_csr_sign(
            csr: $csr,
            ca_certificate: null,
            private_key: $privkey,
            days: 365,
            options: [
                'digest_alg' => 'sha256',
                'x509_extensions' => 'x509_ext',
                'config' => $configFilePath
            ]
        );
        if ($x509 === false) {
            throw new \RuntimeException("Could not generate certificate: " . implode(", ", $this->getOpenSslErrors()));
        }

        // Close tmp config file
        fclose($configFile);

        // Export certificate and private key
        openssl_x509_export($x509, $certout);
        openssl_pkey_export($privkey, $pkeyout);

        return [
            'cert' => $certout,
            'key' => $pkeyout,
        ];
    }

    /**
     * Write the openssl config file to a resource
     * @param resource $configFile
     * @param string $subjectAltName
     * @return void
     */
    protected function writeOpenSslConfig($configFile, string $subjectAltName = ''): void
    {
        if (!is_resource($configFile)) {
            return;
        }

        $config = <<<EOT
[ x509_ext ]
subjectAltName = $subjectAltName
EOT;
        fwrite($configFile, $config);
    }

    protected function buildSubjectAltName(string $uziNumber): string
    {
        $oidCa = UziConstants::OID_CA_SERVER;
        $uziVersion = 1;
        $cardType = 'S';
        $subscriberNumber = '90000123';
        $role = '00.000';
        $agbCode = '00000000';

        return 'otherName:2.5.5.5;IA5STRING:' . implode('-', [
            $oidCa,
            $uziVersion,
            $uziNumber,
            $cardType,
            $subscriberNumber,
            $role,
            $agbCode,
        ]);
    }

    protected function getOpenSslErrors(): array
    {
        $errors = [];
        while (($e = openssl_error_string()) !== false) {
            $errors[] = $e;
        }
        return $errors;
    }
}
