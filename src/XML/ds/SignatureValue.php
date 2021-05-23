<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\XMLBase64ElementTrait;

/**
 * Class representing a ds:SignatureValue element.
 *
 * @package simplesaml/xml-security
 */
final class SignatureValue extends AbstractDsElement
{
    use XMLBase64ElementTrait;


    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->setContent($content);
    }
}
