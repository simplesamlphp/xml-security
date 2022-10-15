<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use SimpleSAML\XML\Base64ElementTrait;

/**
 * Class representing a xenc:OAEPparams element.
 *
 * @package simplesaml/xml-security
 * @psalm-suppress PropertyNotSetInConstructor $content
 */
final class OAEPparams extends AbstractXencElement
{
    use Base64ElementTrait;


    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->setContent($content);
    }
}
