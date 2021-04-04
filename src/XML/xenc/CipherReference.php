<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\XMLElementInterface;

/**
 * Class representing the <xenc:CipherReference> element.
 *
 * @package simplesamlphp/xml-security
 */
class CipherReference extends AbstractReference
{
    /**
     * CipherReference constructor.
     *
     * @param string $uri
     * @param \SimpleSAML\XML\XMLElementInterface[] $elements
     */
    public function __construct(string $uri, array $elements = [])
    {
        Assert::allIsInstanceOf($elements, XMLElementInterface::class);
        parent::__construct($uri, $elements);
    }
}
