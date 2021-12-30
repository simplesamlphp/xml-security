<?php

require_once('../vendor/autoload.php');

use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Test\XML\CustomSignable;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;

$chunk = DOMDocumentFactory::fromString('<ssp:Some>Chunk</ssp:Some>')->documentElement;
$signable = new CustomSignable($chunk);

$privateKey = PEMCertificatesMock::getPrivateKey(C::SIG_RSA_SHA256, PEMCertificatesMock::SELFSIGNED_PRIVATE_KEY);
$x = $signable->sign($privateKey);
echo $x;
