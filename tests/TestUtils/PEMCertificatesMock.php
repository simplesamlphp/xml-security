<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\TestUtils;

use Exception;
use SimpleSAML\XMLSecurity\Key\PrivateKey;
use SimpleSAML\XMLSecurity\Key\PublicKey;
use SimpleSAML\XMLSecurity\Utils\Certificate as CertificateUtils;

use function dirname;
use function file_get_contents;
use function preg_match;
use function preg_replace;

/**
 * Class \SimpleSAML\TestUtils\PEMCertificatesMock
 */
class PEMCertificatesMock
{
    public const ALG_RSA = 'rsa';
    public const ALG_DSA = 'dsa';

    public const CERTIFICATE_DIR_RSA = 'resources/certificates/rsa-pem';
    public const CERTIFICATE_DIR_DSA = 'resources/certificates/dsa-pem';

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
     * @param string $file The file to use
     * @param string $algorithm  One of rsa|dsa
     * @return string
     */
    private static function buildPath(string $file, string $algorithm): string
    {
        $base = dirname(dirname(__FILE__));
        switch ($algorithm) {
            case 'rsa':
                return $base . DIRECTORY_SEPARATOR . self::CERTIFICATE_DIR_RSA . DIRECTORY_SEPARATOR . $file;
            case 'dsa':
                return $base . DIRECTORY_SEPARATOR . self::CERTIFICATE_DIR_RSA . DIRECTORY_SEPARATOR . $file;
            default:
                throw new Exception(
                    sprintf('Invalid algorithm \'%s\', must be one of \'rsa\' or \'dsa\'', $algorithm)
                );
        }
    }


    /**
     * @param string $file The file we should load
     * @param string $algorithm  One of rsa|dsa
     * @return string The file contents
     */
    public static function loadPlainCertificateFile(string $file, string $algorithm = self::ALG_RSA): string
    {
        return file_get_contents(self::buildPath($file, $algorithm));
    }


    /**
     * @param string $file The file to use
     * @param string $algorithm  One of rsa|dsa
     * @return \SimpleSAML\XMLSecurity\Key\PublicKey
     */
    public static function getPublicKey(string $file, string $algorithm = self::ALG_RSA): PublicKey
    {
        $path = self::buildPath($file, $algorithm);
        return PublicKey::fromFile($path);
    }


    /**
     * @param string $file The file to use
     * @param string $algorithm  One of rsa|dsa
     * @return \SimpleSAML\XMLSecurity\Key\PrivateKey
     */
    public static function getPrivateKey(string $file, string $algorithm = self::ALG_RSA): PrivateKey
    {
        $path = self::buildPath($file, $algorithm);
        return PrivateKey::fromFile($path);
    }


    /**
     * @param string $file The file to use
     * @param string $algorithm  One of rsa|dsa
     * @return string
     */
    public static function getPlainPublicKey(
        string $file = self::PUBLIC_KEY,
        string $algorithm = self::ALG_RSA
    ): string {
        return self::loadPlainCertificateFile($file, $algorithm);
    }


    /**
     * @param string $file The file to use
     * @param string $algorithm  One of rsa|dsa
     * @return string
     */
    public static function getPlainPrivateKey(
        string $file = self::PRIVATE_KEY,
        string $algorithm = self::ALG_RSA
    ): string {
        return self::loadPlainCertificateFile($file, $algorithm);
    }


    /**
     * @param string $file The file to use
     * @param string $algorithm  One of rsa|dsa
     * @return string
     */
    public static function getPlainPublicKeyContents(
        string $file = self::PUBLIC_KEY,
        string $algorithm = self::ALG_RSA
    ): string {
        return CertificateUtils::stripHeaders(self::loadPlainCertificateFile($file, $algorithm), CertificateUtils::PUBLIC_KEY_PATTERN);
    }


    /**
     * @param string $file The file to use
     * @param string $algorithm  One of rsa|dsa
     * @return string
     */
    public static function getPlainPrivateKeyContents(
        string $file = self::PRIVATE_KEY,
        string $algorithm = self::ALG_RSA
    ): string {
        return CertificateUtils::stripHeaders(self::loadPlainCertificateFile($file, $algorithm), CertificateUtils::PRIVATE_KEY_PATTERN);
    }
}
