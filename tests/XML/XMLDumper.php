<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use Dom;

use function preg_replace;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\XMLDumper
 *
 * @package simplesamlphp/xml-security
 */
final class XMLDumper
{
    public static function dumpDOMDocumentXMLWithBase64Content(Dom\XMLDocument $document): string
    {
        $dump = $document->saveXML($document->documentElement);
        return preg_replace('/ *[\\r\\n] */', '', $dump);
    }
}
