<?php

declare(strict_types=1);

namespace SimpleSAML\XML;

use DOMElement;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\Exception\MissingAttributeException;
use Serializable;
use SimpleSAML\Assert\Assert;

/**
 * Abstract class to be implemented by all signed classes
 *
 * @package simplesamlphp/xml-security
 */
abstract class AbstractSignedXMLElement implements SignedElementInterface
{
    use SignedElementTrait;

    /**
     * Create a document structure for this element
     *
     * @param \DOMElement|null $parent The element we should append to.
     * @return \DOMElement
     */
    /**
     * The signed DOM structure.
     *
     * @var \DOMElement
     */
    protected DOMElement $structure;

    /**
     * The unsigned elelement.
     *
     * @var \SimpleSAML\XML\AbstractXMLElement
     */
    protected AbstractXMLElement $elt;
}
