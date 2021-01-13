<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Utils;

use Exception;

/**
 * Collection of Utility functions specifically for certificates
 */
class Certificate
{
    /**
     * The pattern that the contents of a certificate should adhere to
     */
    public const PUBLIC_KEY_PATTERN = '/^-----BEGIN CERTIFICATE-----([^-]*)^-----END CERTIFICATE-----/m';
    public const PRIVATE_KEY_PATTERN = '/^-----BEGIN RSA PRIVATE KEY-----([^-]*)^-----END RSA PRIVATE KEY-----/m';


    /**
     * @param string $certificate
     * @param string $pattern
     *
     * @return bool
     */
    public static function hasValidStructure(string $certificate, string $pattern = self::PUBLIC_KEY_PATTERN): bool
    {
        return !!preg_match($pattern, $certificate);
    }


    /**
     * @param string $X509CertificateContents
     *
     * @return string
     */
    public static function convertToCertificate(string $X509CertificateContents): string
    {
        return "-----BEGIN CERTIFICATE-----\n"
                . chunk_split($X509CertificateContents, 64, "\n")
                . "-----END CERTIFICATE-----";
    }


    /**
     * @param string $key The PEM-encoded key
     * @param string $pattern The pattern to use
     * @return string The stripped key
     */
    public static function stripHeaders(string $key, string $pattern = self::PUBLIC_KEY_PATTERN)
    {
        $matches = [];
        $result = preg_match($pattern, $key, $matches);
        if ($result === false) {
            throw new Exception('Could not find content matching the provided pattern.');
        }

        /** @psalm-suppress EmptyArrayAccess */
        return preg_replace('/\s+/', '', $matches[1]);
    }
}
