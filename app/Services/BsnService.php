<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Services\Bsn\BsnResolveService;
use MinVWS\Crypto\Laravel\Factory;
use MinVWS\Crypto\Laravel\SealboxCryptoInterface;
use App\Exceptions\MonsterException;

/**
 * Class BsnResolveService
 */
class BsnService
{
    protected BsnResolveService $resolver;

    public function __construct(BsnResolveService $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param string $bsn
     * @param string $dob
     * @param User $user
     * @return array
     * @throws MonsterException
     * @throws \SodiumException
     */
    public function getInfo(string $bsn, string $dob, User $user): array
    {
        /// Generate ephemeral key for communication with Monster
        $kp = sodium_crypto_box_keypair();
        $pubKey = base64_encode(sodium_crypto_box_publickey($kp));
        $privKey = base64_encode(sodium_crypto_box_secretkey($kp));

        // Get Monster's public key and encrypt data with it.
        $pubKeyMonster = config('monster.pubkey');


        $sealbox = Factory::createSealboxCryptoService(
            privKey: $privKey,
            recipientPubKey: $pubKeyMonster
        );

        // Resolve BSN info
        $info = $this->resolver->resolveBsn(
            $user,
            $pubKey,
            base64_encode($sealbox->encrypt($bsn)),
            base64_encode($sealbox->encrypt($dob))
        );

        // To decrypt data use our own priv/pub set
        $sealboxDecrypt = Factory::createSealboxCryptoService(
            privKey: $privKey,
            recipientPubKey: $pubKey
        );

        return $this->decryptFields($sealboxDecrypt, $info);
    }

    protected function decryptFields(SealboxCryptoInterface $sealbox, array $info): array
    {
        foreach ($info as $k => $v) {
            if ($k == "jwt") {
                continue;
            }

            if (is_array($v)) {
                $info[$k] = $this->decryptFields($sealbox, $v);
            } else {
                $s = hex2bin($v);
                if ($s === false) {
                    $s = $v;
                }
                $info[$k] = $sealbox->decrypt($s);
            }
        }
        return $info;
    }
}
