<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Utils;

/**
 * Collection of Utility functions specifically for certificates
 */
class Certificate
{
    /**
     * The pattern that the contents of a certificate should adhere to
     */
    public const CERTIFICATE_PATTERN = '/^-----BEGIN CERTIFICATE-----([^-]*)^-----END CERTIFICATE-----/m';


    /**
     * @param string $certificate
     *
     * @return bool
     */
    public static function hasValidStructure(string $certificate): bool
    {
        return !!preg_match(self::CERTIFICATE_PATTERN, $certificate);
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
     * @param array|string $issuer
     *
     * @return string
     */
    public static function parseIssuer($issuer): string
    {
        if (is_array($issuer)) {
            $parts = [];
            foreach ($issuer as $key => $value) {
                array_unshift($parts, $key . '=' . $value);
            }
            return implode(',', $parts);
        }

        return $issuer;
    }
}
