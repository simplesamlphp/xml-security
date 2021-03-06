<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

/**
 * Class representing the <xenc:KeyReference> element.
 *
 * @package simplesamlphp/xml-security
 */
class KeyReference extends AbstractReference
{
    /**
     * KeyReference constructor.
     *
     * @param string $uri
     * @param \SimpleSAML\XML\Chunk[] $references
     */
    public function __construct(string $uri, array $references = [])
    {
        parent::__construct($uri, $references);
    }
}
