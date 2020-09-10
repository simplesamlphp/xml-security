<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

/**
 * Class representing the <xenc:DataReference> element.
 *
 * @package simplesamlphp/saml2
 */
class DataReference extends AbstractReference
{
    /**
     * DataReference constructor.
     *
     * @param string $uri
     * @param \SimpleSAML\XML\Chunk[] $references
     */
    public function __construct(string $uri, array $references = [])
    {
        parent::__construct($uri, $references);
    }
}
