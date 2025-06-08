<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\dsig11;

use SimpleSAML\XML\IntegerElementTrait;

/**
 * Class representing a dsig11:CoFactor element.
 *
 * @package simplesaml/xml-security
 */
final class CoFactor extends AbstractDsig11Element
{
    use IntegerElementTrait;


    /**
     * Initialize a CoFactor element.
     *
     * @param string $value
     */
    public function __construct(
        string $value,
    ) {
        $this->setContent($value);
    }
}
