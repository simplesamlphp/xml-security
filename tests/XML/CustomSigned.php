<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XML\Utils as XMLUtils;
use SimpleSAML\XMLSecurity\XML\ds\Signature;
use SimpleSAML\XMLSecurity\XML\AbstractSignedXMLElement;
use SimpleSAML\XMLSecurity\XML\SignedElementInterface;
use SimpleSAML\XMLSecurity\XML\SignedElementTrait;

/**
 * @package simplesamlphp\saml2
 */
final class CustomSigned extends AbstractSignedXMLElement
{
    use SignedElementTrait;


    /**
     * Create a class from XML
     *
     * @param \DOMElement $xml
     * @return self
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same($xml->localName, 'CustomSignable', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, CustomSignable::NS, InvalidDOMElementException::class);

//var_dump($xml->ownerDocument->saveXML());
        $signatureElement = XMLUtils::xpQuery($xml, './ds:Signature');
        Assert::minCount($signatureElement, 1, MissingElementException::class);
        Assert::minCount($signatureElement, 1, TooManyElementsException::class);
//        $signature = Signature::getChildrenOfClass($xml);
//        Assert::minCount($signature, 1, MissingElementException::class);
//        Assert::minCount($signature, 1, TooManyElementsException::class);

        $clone = clone $signatureElement[0];
        $signature = Signature::fromXML($clone);

        return new self(
            $xml,
            CustomSignable::fromXML($xml),
            $signature
        );
    }
}
