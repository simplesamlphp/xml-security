<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc11;

use SimpleSAML\XML\{SchemaValidatableElementInterface, SchemaValidatableElementTrait};
use SimpleSAML\XML\Type\AnyURIValue;

/**
 * Class representing <xenc11:AbstractMGFType>.
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractMGFType extends AbstractAlgorithmIdentifierType implements
    SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;

    /**
     * MGFType constructor.
     *
     * @param \SimpleSAML\XML\Type\AnyURIValue $Algorithm
     */
    public function __construct(
        AnyURIValue $Algorithm,
    ) {
        parent::__construct($Algorithm, null);
    }
}
