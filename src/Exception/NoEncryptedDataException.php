<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Class NoEncryptedData
 *
 * This exception is thrown when we can't find encrypted data in a given DOM document or element.
 *
 * @package simplesamlphp/xml-security
 */
class NoEncryptedDataException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("There is no EncryptedData in the document or element.");
    }
}
