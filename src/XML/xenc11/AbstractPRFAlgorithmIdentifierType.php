<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc11;

use SimpleSAML\XML\Type\AnyURIValue;

/**
 * Class representing <xenc11:AbstractPRFAlgorithmIdentifierType>.
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractPRFAlgorithmIdentifierType extends AbstractAlgorithmIdentifierType
{
    /**
     * AlgorithmPRFIdentifierType constructor.
     *
     * @param \SimpleSAML\XML\Type\AnyURIValue $Algorithm
     */
    public function __construct(
        AnyURIValue $Algorithm,
    ) {
        parent::__construct($Algorithm, null);
    }
}
