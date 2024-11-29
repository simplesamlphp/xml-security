<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\ds;

use SimpleSAML\XML\StringElementTrait;

/**
 * Class representing a ds:MgmtData element.
 *
 * @package simplesamlphp/xml-security
 */
final class MgmtData extends AbstractDsElement
{
    use StringElementTrait;


    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->setContent($content);
    }
}
