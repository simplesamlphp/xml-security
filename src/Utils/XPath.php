<?php

namespace SimpleSAML\XMLSecurity\Utils;

use DOMDocument;
use DOMNode;
use DOMXPath;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\RuntimeException;

/**
 * Compilation of utilities for XPath.
 *
 * @package SimpleSAML\XMLSecurity\Utils
 */
class XPath extends \RobRichards\XMLSecLibs\Utils\XPath
{
    /**
     * Get a DOMXPath object that can be used to search for XMLDSIG elements.
     *
     * @param \DOMDocument $doc The document to associate to the DOMXPath object.
     *
     * @return \DOMXPath A DOMXPath object ready to use in the given document, with the XMLDSIG namespace already
     * registered.
     */
    public static function getXPath(DOMDocument $doc)
    {
        $xp = new DOMXPath($doc);
        $xp->registerNamespace('ds', C::XMLDSIGNS);
        $xp->registerNamespace('xenc', C::XMLENCNS);
        return $xp;
    }


    /**
     * Search for an element with a certain name among the children of a reference element.
     *
     * @param \DOMNode $ref The DOMDocument or DOMElement where encrypted data is expected to be found as a child.
     * @param string $name The name (possibly prefixed) of the element we are looking for.
     *
     * @return \DOMElement|false The element we are looking for, or false when not found.
     *
     * @throws RuntimeException If no DOM document is available.
     */
    public static function findElement(DOMNode $ref, $name)
    {
        $doc = $ref instanceof DOMDocument ? $ref : $ref->ownerDocument;
        if ($doc === null) {
            throw new RuntimeException('Cannot search, no DOM document available');
        }

        $nodeset = self::getXPath($doc)->query('./'.$name, $ref);

        if ($nodeset->length === 0) {
            return false;
        }
        return $nodeset->item(0);
    }
}
