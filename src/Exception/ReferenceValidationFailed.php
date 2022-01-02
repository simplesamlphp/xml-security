<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Class ReferenceValidationFailedException
 *
 * This exception is thrown when we can't validate the signature against the referenced DOMDocument or DOMElement.
 *
 * @package simplesamlphp/xml-security
 */
class ReferenceValidationFailedException extends SignatureVerificationFailedException
{
    /**
     * @param string|null $message
     */
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?: 'Reference validation failed.');
    }
}
