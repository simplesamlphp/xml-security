<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Backend;

/**
 * Capability interface for backends that support configuring OAEP digest and MGF parameters.
 *
 * Must be called after the cipher/algorithm has been configured via setCipher().
 * (Re)configuring the cipher resets both parameters to null (= algorithm default).
 * Calling setOAEPParams() before any cipher is configured is a programming error and throws RuntimeException.
 * An unsupported digest/mgf value or combination throws UnsupportedAlgorithmException.
 *
 * @package simplesamlphp/xml-security
 */
interface OAEPParametersAware
{
    /**
     * Reconfigure the OAEP digest/MGF parameters, e.g. as read from an <xenc:EncryptionMethod> element.
     *
     * @param string|null $digestAlg Digest algorithm URI for OAEP (e.g. xmldsig#sha1). Null = algorithm default.
     * @param string|null $mgf Mask generation function URI for OAEP (xenc11). Null = algorithm default.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException If the digest/mgf value or
     *   combination is unsupported.
     * @throws \SimpleSAML\XMLSecurity\Exception\RuntimeException If no cipher has been configured yet.
     */
    public function setOAEPParams(?string $digestAlg = null, ?string $mgf = null): void;
}
