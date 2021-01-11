<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML;

use Exception;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSecurity\XML\ds\Signature;
use SimpleSAML\XMLSecurity\XMLSecurityKey;

/**
 * Helper trait for processing signed elements.
 *
 * @package simplesamlphp/xml-security
 */
trait SignedElementTrait
{
    /**
     * The signature of this element.
     *
     * @var \SimpleSAML\XMLSecurity\XML\ds\Signature $signature
     */
    protected Signature $signature;


    /**
     * Get the signature element of this object.
     *
     * @return \SimpleSAML\XMLSecurity\XML\ds\Signature
     */
    public function getSignature(): ?Signature
    {
        return $this->signature;
    }


    /**
     * Initialize a signed element from XML.
     *
     * @param \SimpleSAML\XMLSecurity\XML\ds\Signature $signature The ds:Signature object
     */
    protected function setSignature(Signature $signature): void
    {
        $this->signature = $signature;
    }


    /**
     * Validate this element against a public key.
     *
     * true is returned on success, false is returned if we don't have any
     * signature we can validate. An exception is thrown if the signature
     * validation fails.
     *
     * @param  \SimpleSAML\XMLSecurity\XMLSecurityKey $key The key we should check against.
     * @return bool True on success, false when we don't have a signature.
     * @throws \Exception
     */
    public function validate(XMLSecurityKey $key): bool
    {
        if ($this->signature === null) {
            return false;
        }

        $signer = $this->signature->getSigner();
        Assert::eq(
            $key->getAlgorithm(),
            $this->signature->getAlgorithm(),
            'Algorithm provided in key does not match algorithm used in signature.'
        );

        // check the signature
        if ($signer->verify($key) === 1) {
            return true;
        }

        throw new Exception("Unable to validate Signature");
    }


    /**
     * Retrieve certificates that sign this element.
     *
     * @return array Array with certificates.
     * @throws \Exception if an error occurs while trying to extract the public key from a certificate.
     */
    public function getValidatingCertificates(): array
    {
        $ret = [];
        foreach ($this->signature->getCertificates() as $cert) {
            // extract the public key from the certificate for validation.
            $key = new XMLSecurityKey($this->signature->getAlgorithm(), ['type' => 'public']);
            $key->loadKey($cert);

            try {
                // check the signature.
                if ($this->validate($key)) {
                    $ret[] = $cert;
                }
            } catch (Exception $e) {
                // this certificate does not sign this element.
            }
        }

        return $ret;
    }
}
