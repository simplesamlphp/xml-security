<?php

declare(strict_types=1);

return [
    'http://www.w3.org/2000/09/xmldsig#' => [
        'CanonicalizationMethod' => '\SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethod',
        'DigestMethod' => '\SimpleSAML\XMLSecurity\XML\ds\DigestMethod',
        'DigestValue' => '\SimpleSAML\XMLSecurity\XML\ds\DigestValue',
        'DSAKeyValue' => '\SimpleSAML\XMLSecurity\XML\ds\DSAKeyValue',
        'KeyInfo' => '\SimpleSAML\XMLSecurity\XML\ds\KeyInfo',
        'KeyName' => '\SimpleSAML\XMLSecurity\XML\ds\KeyName',
        'KeyValue' => '\SimpleSAML\XMLSecurity\XML\ds\KeyValue',
        'Manifest' => '\SimpleSAML\XMLSecurity\XML\ds\Manifest',
        'MgmtData' => '\SimpleSAML\XMLSecurity\XML\ds\MgmtData',
        'Object' => '\SimpleSAML\XMLSecurity\XML\ds\DsObject',
        'PGPData' => '\SimpleSAML\XMLSecurity\XML\ds\PGPData',
        'Reference' => '\SimpleSAML\XMLSecurity\XML\ds\Reference',
        'RetrievalMethod' => '\SimpleSAML\XMLSecurity\XML\ds\RetrievalMethod',
        'RSAKeyValue' => '\SimpleSAML\XMLSecurity\XML\ds\RSAKeyValue',
        'Signature' => '\SimpleSAML\XMLSecurity\XML\ds\Signature',
        'SignatureMethod' => '\SimpleSAML\XMLSecurity\XML\ds\SignatureMethod',
        'SignatureProperties' => '\SimpleSAML\XMLSecurity\XML\ds\SignatureProperties',
        'SignatureProperty' => '\SimpleSAML\XMLSecurity\XML\ds\SignatureProperty',
        'SignatureValue' => '\SimpleSAML\XMLSecurity\XML\ds\SignatureValue',
        'SignedInfo' => '\SimpleSAML\XMLSecurity\XML\ds\SignedInfo',
        'SPKIData' => '\SimpleSAML\XMLSecurity\XML\ds\SPKIData',
        'Transform' => '\SimpleSAML\XMLSecurity\XML\ds\Transform',
        'Transforms' => '\SimpleSAML\XMLSecurity\XML\ds\Transforms',
        'X509Data' => '\SimpleSAML\XMLSecurity\XML\ds\X509Data',
    ],
    'http://www.w3.org/2009/xmldsig11#' => [
//        'DEREncodedKeyValue' => '\SimpleSAML\XMLSecurity\XML\dsig11\DEREncodedKeyValue',
//        'ECKeyValue' => '\SimpleSAML\XMLSecurity\XML\dsig11\ECKeyValue',
//        'GnB' => '\SimpleSAML\XMLSecurity\XML\dsig11\GnB',
        'KeyInfoReference' => '\SimpleSAML\XMLSecurity\XML\dsig11\KeyInfoReference',
//        'PnB' => '\SimpleSAML\XMLSecurity\XML\dsig11\PnB',
//        'Prime' => '\SimpleSAML\XMLSecurity\XML\dsig11\Prime',
//        'TnB' => '\SimpleSAML\XMLSecurity\XML\dsig11\TnB',
        'X509Digest' => '\SimpleSAML\XMLSecurity\XML\dsig11\X509Digest',
    ],
    'http://www.w3.org/2001/10/xml-exc-c14n#' => [
        'InclusiveNamespaces' => '\SimpleSAML\XMLSecurity\XML\ec\InclusiveNamespaces',
    ],
    'http://www.w3.org/2001/04/xmlenc#' => [
        'AgreementMethod' => '\SimpleSAML\XMLSecurity\XML\xenc\AgreementMethod',
        'CipherData' => '\SimpleSAML\XMLSecurity\XML\xenc\CipherData',
        'CipherReference' => '\SimpleSAML\XMLSecurity\XML\xenc\CipherReference',
        'DHKeyValue' => '\SimpleSAML\XMLSecurity\XML\xenc\DHKeyValue',
        'EncryptedData' => '\SimpleSAML\XMLSecurity\XML\xenc\EncryptedData',
        'EncryptedKey' => '\SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey',
        'EncryptionProperties' => '\SimpleSAML\XMLSecurity\XML\xenc\EncryptionProperties',
        'EncryptionProperty' => '\SimpleSAML\XMLSecurity\XML\xenc\EncryptionProperty',
        'ReferenceList' => '\SimpleSAML\XMLSecurity\XML\xenc\ReferenceList',
    ],
    'http://www.w3.org/2009/xmlenc11#'=> [
        'ConcatKDFParams' => '\SimpleSAML\XMLSecurity\XML\xenc11\ConcatKDFParams',
        'DerivedKey' => '\SimpleSAML\XMLSecurity\XML\xenc11\DerivedKey',
        'KeyDerivationMethod' => '\SimpleSAML\XMLSecurity\XML\xenc11\KeyDerivationMethod',
        'PBKDF2-params' => '\SimpleSAML\XMLSecurity\XML\xenc11\PBKDF2params',
        'MGF' => '\SimpleSAML\XMLSecurity\XML\xenc11\MGF',
    ],
];
