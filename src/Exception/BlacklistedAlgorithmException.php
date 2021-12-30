<?php

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Class BlacklistedAlgorithmException
 *
 * This exception is thrown when the algorithm used is on a blacklist
 *
 * @package simplesamlphp/xml-security
 */
class BlacklistedAlgorithmException extends SignatureVerificationFailedException
{
    /**
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?: 'Blacklisted algorithm.');
    }
}
