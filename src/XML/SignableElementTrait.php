<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML;

use DOMElement;
use DOMNode;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XMLSecurity\Alg\SignatureAlgorithm;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\RuntimeException;
use SimpleSAML\XMLSecurity\Utils\Security;
use SimpleSAML\XMLSecurity\Utils\XML;
use SimpleSAML\XMLSecurity\XML\ds\CanonicalizationMethod;
use SimpleSAML\XMLSecurity\XML\ds\DigestMethod;
use SimpleSAML\XMLSecurity\XML\ds\DigestValue;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\ds\Reference;
use SimpleSAML\XMLSecurity\XML\ds\Signature;
use SimpleSAML\XMLSecurity\XML\ds\SignatureMethod;
use SimpleSAML\XMLSecurity\XML\ds\SignatureValue;
use SimpleSAML\XMLSecurity\XML\ds\SignedInfo;
use SimpleSAML\XMLSecurity\XML\ds\Transform;
use SimpleSAML\XMLSecurity\XML\ds\Transforms;

/**
 * Trait SignableElementTrait
 *
 * @package simplesamlphp/xml-security
 */
trait SignableElementTrait
{
    /** @var \SimpleSAML\XMLSecurity\XML\ds\Signature|null */
    protected ?Signature $signature = null;

    /** @var string */
    private string $c14nAlg = Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS;

    /** @var \SimpleSAML\XMLSecurity\XML\ds\KeyInfo|null */
    private ?KeyInfo $keyInfo = null;

    /** @var \SimpleSAML\XMLSecurity\Alg\SignatureAlgorithm|null */
    private ?SignatureAlgorithm $signer = null;


    /**
     * Get the ID of this element.
     *
     * When this method returns null, the signature created for this object will reference the entire document.
     *
     * @return string|null The ID of this element, or null if we don't have one.
     */
    abstract public function getId(): ?string;


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
        string $canonicalizationAlg = Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS,
        ?KeyInfo $keyInfo = null
    ): void {
        $this->signer = $signer;
        $this->keyInfo = $keyInfo;
        Assert::oneOf(
            $canonicalizationAlg,
            [
                Constants::C14N_INCLUSIVE_WITH_COMMENTS,
                Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS,
                Constants::C14N_EXCLUSIVE_WITH_COMMENTS,
                Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS
            ],
            'Unsupported canonicalization algorithm',
            InvalidArgumentException::class
        );
        $this->c14nAlg = $canonicalizationAlg;
    }


    /**
     * @param \DOMElement $xml
     * @throws \Exception
     */
    private function doSign(DOMElement $xml): void
    {
        Assert::notNull(
            $this->signer,
            'Cannot call toSignedXML() without calling sign() first.',
            RuntimeException::class
        );

        $algorithm = $this->signer->getAlgorithmId();
        $digest = $this->signer->getDigest();

        $transforms = new Transforms([
            new Transform(Constants::XMLDSIG_ENVELOPED),
            new Transform($this->c14nAlg)
        ]);

        $refId = $this->getId();
        $reference = new Reference(
            new DigestMethod($digest),
            new DigestValue(Security::hash($digest, XML::processTransforms($transforms, $xml))),
            $transforms,
            null,
            null,
            ($refId !== null) ? '#' . $refId : null
        );

        $signedInfo = new SignedInfo(
            new CanonicalizationMethod($this->c14nAlg),
            new SignatureMethod($algorithm),
            [$reference]
        );

        $signingData = $signedInfo->canonicalize($this->c14nAlg);
        $signedData = base64_encode($this->signer->sign($signingData));

        $this->signature = new Signature($signedInfo, new SignatureValue($signedData), $this->keyInfo);
    }


    /**
     * @param DOMElement $root
     * @param DOMNode $node
     * @param DOMElement $signature
     * @return DOMElement
     */
    private function insertBefore(DOMElement $root, DOMNode $node, DOMElement $signature): DOMElement
    {
        $root->removeChild($signature);
        return $root->insertBefore($signature, $node);
    }
}
