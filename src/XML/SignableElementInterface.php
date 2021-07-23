<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML;

use SimpleSAML\XMLSecurity\Alg\SignatureAlgorithm;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;

/**
 * An interface describing objects that can be signed.
 *
 * @package simplesamlphp/xml-security
 */
interface SignableElementInterface extends CanonicalizableElementInterface
{
    /**
     * Get the ID of this element.
     *
     * When this method returns null, the signature created for this object will reference the entire document.
     *
     * @return string|null The ID of this element, or null if we don't have one.
     */
    public function getId(): ?string;


    /**
     * Sign the current element.
     *
     * @note The signature will not be applied until toSignedXML() is called.
     *
     * @param \SimpleSAML\XMLSecurity\Alg\SignatureAlgorithm $signer The actual signer implementation to use.
     * @param string $canonicalizationAlg The identifier of the canonicalization algorithm to use.
     * @param \SimpleSAML\XMLSecurity\XML\ds\KeyInfo|null $keyInfo A KeyInfo object to add to the signature.
     */
    public function sign(
        SignatureAlgorithm $signer,
        string $canonicalizationAlg,
        ?KeyInfo $keyInfo = null
    ): void;
}
