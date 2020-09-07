<?php

namespace SimpleSAML\XMLSecurity\Exception;

/**
 * Class NoSignatureFound
 *
 * This exception is thrown when we can't find a signature in a given DOM document or element.
 *
 * @package SimpleSAML\XMLSecurity\Exception
 */
class NoSignatureFound extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("There is no signature in the document or element.");
    }
}
