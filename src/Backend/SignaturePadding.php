<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Backend;

/**
 * Interface for backends implementing signature padding.
 *
 * @package simplesamlphp/xml-security
 */
interface SignaturePadding
{
    /**
     * Set the padding method to be used by this backend.
     *
     * @param string $algId The identifier of the signature algorithm.
     */
    public function setSignaturePadding(string $algId): void;
}
