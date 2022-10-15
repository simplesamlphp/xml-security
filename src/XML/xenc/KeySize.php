<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XML\StringElementTrait;

/**
 * Class representing a xenc:KeySize element.
 *
 * @package simplesaml/xml-security
 * @psalm-suppress PropertyNotSetInConstructor $content
 */
final class KeySize extends AbstractXencElement
{
    use StringElementTrait;


    /**
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->setContent($content);
    }


    /**
     * Validate the content of the element.
     *
     * @param string $content  The value to go in the XML textContent
     * @throws \Exception on failure
     * @return void
     */
    protected function validateContent(string $content): void
    {
        Assert::positiveInteger(
            intval($content),
            SchemaViolationException::class
        );
    }
}
