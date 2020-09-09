<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\TestUtils;

use Exception;
use SimpleSAML\XMLSecurity\XMLSecurityKey;

/**
 * Class \SimpleSAML\TestUtils\PEMCertificatesMock
 */
class PEMCertificatesMock
{
    public const ALG_SIG_RSA = 'rsa';
    public const ALG_SIG_DSA = 'dsa';

    public const PUBLIC_KEY_PATTERN = '/^-----BEGIN CERTIFICATE-----([^-]*)^-----END CERTIFICATE-----/m';
    public const PRIVATE_KEY_PATTERN = '/^-----BEGIN RSA PRIVATE KEY-----([^-]*)^-----END RSA PRIVATE KEY-----/m';

    public const CERTIFICATE_DIR_RSA = '/tests/resources/certificates/rsa-pem';
    public const CERTIFICATE_DIR_DSA = '/tests/resources/certificates/dsa-pem';

    public const PUBLIC_KEY = 'signed.simplesamlphp.org.crt';
    public const PRIVATE_KEY = 'signed.simplesamlphp.org_nopasswd.key';
    public const PRIVATE_KEY_PROTECTED = 'signed.simplesamlphp.org.key';
    public const OTHER_PUBLIC_KEY = 'other.simplesamlphp.org.crt';
    public const OTHER_PRIVATE_KEY = 'other.simplesamlphp.org_nopasswd.key';
    public const OTHER_PRIVATE_KEY_PROTECTED = 'other.simplesamlphp.org.key';
    public const SELFSIGNED_PUBLIC_KEY = 'selfsigned.simplesamlphp.org.crt';
    public const SELFSIGNED_PRIVATE_KEY = 'selfsigned.simplesamlphp.org_nopasswd.key';
    public const SELFSIGNED_PRIVATE_KEY_PROTECTED = 'selfsigned.simplesamlphp.org.key';
    public const BROKEN_PUBLIC_KEY = 'broken.simplesamlphp.org.crt';
    public const BROKEN_PRIVATE_KEY = 'broken.simplesamlphp.org.key';
    public const CORRUPTED_PUBLIC_KEY = 'corrupted.simplesamlphp.org.crt';
    public const CORRUPTED_PRIVATE_KEY = 'corrupted.simplesamlphp.org.key';

    /**
     * @param string $key The PEM-encoded key
     * @param bool $private
     * @return string The stripped key
     */
    private static function stripHeaders(string $key, bool $private)
    {
        $matches = [];
        if ($private === false && !preg_match(self::PUBLIC_KEY_PATTERN, $key, $matches)) {
            throw new Exception('Could not find PEM encoded certificate.');
        } elseif ($private === true && !preg_match(self::PRIVATE_KEY_PATTERN, $key, $matches)) {
            throw new Exception('Could not find PEM encoded key.');
        }

        /** @psalm-suppress EmptyArrayAccess */
        return preg_replace('/\s+/', '', $matches[1]);
    }


    /**
     * @param string $file The file we should load
     * @param string $sig_alg  One of rsa|dsa
     * @return string The file contents
     */
    public static function loadPlainCertificateFile(string $file, $sig_alg = self::ALG_SIG_RSA)
    {
        if ($sig_alg === self::ALG_SIG_RSA) {
            return file_get_contents(dirname(dirname(dirname(__FILE__))) . self::CERTIFICATE_DIR_RSA . DIRECTORY_SEPARATOR . $file);
        } else {
            return file_get_contents(dirname(dirname(dirname(__FILE__))) . self::CERTIFICATE_DIR_DSA . DIRECTORY_SEPARATOR . $file);
        }
    }


    /**
     * @param string $hash_alg
     * @param string The file to use
     * @param string $sig_alg  One of rsa|dsa
     * @return \SimpleSAML\XMLSecurity\XMLSecurityKey
     */
    public static function getPublicKey(
        string $hash_alg,
        string $file,
        string $sig_alg = self::ALG_SIG_RSA
    ): XMLSecurityKey {
        $publicKey = new XMLSecurityKey($hash_alg, ['type' => 'public']);
        $publicKey->loadKey(self::getPlainPublicKey($file, $sig_alg));
        return $publicKey;
    }


    /**
     * @param string $hash_alg
     * @param string The file to use
     * @param string $sig_alg  One of rsa|dsa
     * @return \SimpleSAML\XMLSecurity\XMLSecurityKey
     */
    public static function getPrivateKey(
        string $hash_alg,
        string $file,
        string $sig_alg = self::ALG_SIG_RSA
    ): XMLSecurityKey {
        $privateKey = new XMLSecurityKey($hash_alg, ['type' => 'private']);
        $privateKey->loadKey(self::getPlainPrivateKey($file, $sig_alg));
        return $privateKey;
    }


    /**
     * @param string $file The file to use
     * @param string $sig_alg  One of rsa|dsa
     * @return string
     */
    public static function getPlainPublicKey(
        string $file = self::PUBLIC_KEY,
        string $sig_alg = self::ALG_SIG_RSA
    ): string {
        return self::loadPlainCertificateFile($file, $sig_alg);
    }


    /**
     * @param string $file The file to use
     * @param string $sig_alg  One of rsa|dsa
     * @return string
     */
    public static function getPlainPrivateKey(
        string $file = self::PRIVATE_KEY,
        string $sig_alg = self::ALG_SIG_RSA
    ): string {
        return self::loadPlainCertificateFile($file, $sig_alg);
    }


    /**
     * @param string $file The file to use
     * @param string $sig_alg  One of rsa|dsa
     * @return string
     */
    public static function getPlainPublicKeyContents(
        string $file = self::PUBLIC_KEY,
        string $sig_alg = self::ALG_SIG_RSA
    ): string {
        return self::stripHeaders(self::loadPlainCertificateFile($file, $sig_alg), false);
    }


    /**
     * @param string $file The file to use
     * @param string $sig_alg  One of rsa|dsa
     * @return string
     */
    public static function getPlainPrivateKeyContents(
        string $file = self::PRIVATE_KEY,
        string $sig_alg = self::ALG_SIG_RSA
    ): string {
        return self::stripHeaders(self::loadPlainCertificateFile($file, $sig_alg), true);
    }
}
