#!/usr/bin/env php
<?php

require_once(dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php');

use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\ds\X509Certificate;
use SimpleSAML\XMLSecurity\XML\ds\X509Data;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\Test\XML\CustomSignable;

$document = DOMDocumentFactory::fromFile(dirname(dirname(__FILE__)) . '/resources/xml/custom_CustomSignable.xml');

$signer = (new SignatureAlgorithmFactory())->getAlgorithm(
    C::SIG_RSA_SHA256,
    PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::SELFSIGNED_PRIVATE_KEY),
);

$keyInfo = new KeyInfo([
    new X509Data([
         new X509Certificate(PEMCertificatesMock::getPlainCertificateContents(
             PEMCertificatesMock::SELFSIGNED_CERTIFICATE
         ))
    ])
]);

$unsignedElement = CustomSignable::fromXML($document->documentElement);
$unsignedElement->sign($signer, C::C14N_EXCLUSIVE_WITHOUT_COMMENTS, $keyInfo);

echo $unsignedElement->toXML()->ownerDocument->saveXML();
