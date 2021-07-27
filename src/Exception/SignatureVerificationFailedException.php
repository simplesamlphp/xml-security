<?php

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Class SignatureVerificationFailedException
 *
 * This exception is thrown when we can't verify the signature for a given DOMDocument or DOMElement.
 *
 * @package simplesamlphp/xml-security
 */
class SignatureVerificationFailedException extends RuntimeException
{
    /**
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?: 'Signature verification failed.');
    }
}
