<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML;

use DOMElement;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\CanonicalizationFailedException;

/**
 * A trait implementing the CanonicalizableElementInterface.
 *
 * @package simplesamlphp/xml-security
 */
trait CanonicalizableElementTrait
{
    /**
     * This trait uses the php DOM extension. As such, it requires you to keep track (or produce) the DOMElement
     * necessary to perform the canonicalisation.
     *
     * Implement this method to return the DOMElement with the proper representation of this object. Whatever is
     * returned here will be used both to perform canonicalisation and to serialize the object, so that it can be
     * recovered later in its exact original state.
     *
     * @return \DOMElement
     */
    abstract protected function getOriginalXML(): DOMElement;


    /**
     * Canonicalize any given node.
     *
     * @param \DOMElement $element The DOM element that needs canonicalization.
     * @param string $c14nMethod The identifier of the canonicalization algorithm to use.
     *   See \SimpleSAML\XMLSecurity\Constants.
     * @param array<mixed>|null $xpaths An array of xpaths to filter the nodes by. Defaults to null (no filters).
     * @param string[]|null $prefixes An array of namespace prefixes to filter the nodes by.
     *   Defaults to null (no filters).
     *
     * @return string The canonical representation of the given DOM node, according to the algorithm requested.
     */
    public function canonicalizeData(
        DOMElement $element,
        string $c14nMethod,
        ?array $xpaths = null,
        ?array $prefixes = null,
    ): string {
        $withComments = match ($c14nMethod) {
            C::C14N_EXCLUSIVE_WITH_COMMENTS, C::C14N_INCLUSIVE_WITH_COMMENTS => true,
            default => false,
        };
        $exclusive = match ($c14nMethod) {
            C::C14N_EXCLUSIVE_WITH_COMMENTS, C::C14N_EXCLUSIVE_WITHOUT_COMMENTS => true,
            default => false,
        };

        if (
            is_null($xpaths)
            && ($element->ownerDocument !== null)
            && ($element->ownerDocument->documentElement !== null)
            && $element->isSameNode($element->ownerDocument->documentElement)
        ) {
            // check for any PI or comments as they would have been excluded
            $current = $element;
            for ($refNode = $current->previousSibling; $refNode !== null; $current = $refNode) {
                if (
                    (($refNode->nodeType === XML_COMMENT_NODE) && $withComments)
                    || $refNode->nodeType === XML_PI_NODE
                ) {
                    break;
                }
            }

            if ($refNode === null) {
                $element = $element->ownerDocument;
            }
        }

        /** @var string|false $ret */
        $ret = $element->C14N($exclusive, $withComments, $xpaths, $prefixes);
        if ($ret === false) {
            // GHSA-h25p-2wxc-6584
            throw new CanonicalizationFailedException();
        }
        return $ret;
    }


    /**
     * Get the canonical (string) representation of this object.
     *
     * Note that if this object was created using fromXML(), it might be necessary to keep the original DOM
     * representation of the object.
     *
     * @param string $method The canonicalization method to use.
     * @param string[]|null $xpaths An array of XPaths to filter the nodes by. Defaults to null (no filters).
     * @param string[]|null $prefixes An array of namespace prefixes to filter the nodes by. Defaults to null (no
     * filters).
     */
    public function canonicalize(string $method, ?array $xpaths = null, ?array $prefixes = null): string
    {
        return $this->canonicalizeData($this->getOriginalXML(), $method, $xpaths, $prefixes);
    }


    /**
     * Serialize this canonicalisable element.
     *
     * @return array{0: string} The serialized chunk.
     */
    public function __serialize(): array
    {
        $xml = $this->getOriginalXML();
        return [$xml->ownerDocument->saveXML($xml)];
    }
}
