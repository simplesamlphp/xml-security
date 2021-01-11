<?php

require_once('../vendor/autoload.php');

use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Test\XML\CustomSignable;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XMLSecurityKey;

$chunk = new Chunk(DOMDocumentFactory::fromString('<some>Chunk</some>')->documentElement);
$signable = new CustomSignable($chunk);

$privateKey = PEMCertificatesMock::getPrivateKey(XMLSecurityKey::RSA_SHA256, PEMCertificatesMock::SELFSIGNED_PRIVATE_KEY);
$x = $signable->sign($privateKey);
echo $x;
//var_dump($x);
