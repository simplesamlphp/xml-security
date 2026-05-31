<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Utils;

use Dom;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XPath\XPath as XPathUtils;

/**
 * Compilation of utilities for XPath.
 *
 * @package simplesamlphp/xml-security
 */
class XPath extends XPathUtils
{
    /**
     * Get a Dom\XPath object that can be used to search for XMLDSIG elements.
     *
     * @param \Dom\Node $node The document to associate to the Dom\XPath object.
     * @param bool $autoregister Whether to auto-register all namespaces used in the document
     *
     * @return \Dom\XPath A \Dom\XPath object ready to use in the given document, with the XMLDSIG namespace already
     * registered.
     */
    public static function getXPath(Dom\Node $node, bool $autoregister = false): Dom\XPath
    {
        $xp = parent::getXPath($node, $autoregister);

        $xp->registerNamespace('ds', C::NS_XDSIG);
        $xp->registerNamespace('xenc', C::NS_XENC);

        return $xp;
    }
}
