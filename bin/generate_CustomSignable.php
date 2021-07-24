<?php

require_once('../vendor/autoload.php');

use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Test\XML\CustomSignable;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XMLSecurityKey;

$chunk = DOMDocumentFactory::fromString('<ssp:Some>Chunk</ssp:Some>')->documentElement;
$signable = new CustomSignable($chunk);

$privateKey = PEMCertificatesMock::getPrivateKey(XMLSecurityKey::RSA_SHA256, PEMCertificatesMock::SELFSIGNED_PRIVATE_KEY);
$x = $signable->sign($privateKey);
echo $x;
