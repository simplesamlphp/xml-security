<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\Base64StringElementTrait;

/**
 * Class representing a ds:X509Certificate element.
 *
 * @package simplesamlphp/xml-security
 */
final class X509Certificate extends AbstractDsElement
{
    use Base64StringElementTrait;


    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->setContent($content);
    }
}
