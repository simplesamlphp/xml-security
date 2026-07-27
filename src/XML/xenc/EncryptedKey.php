<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use Dom;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Chunk;
use SimpleSAML\XML\SchemaValidatableElementInterface;
use SimpleSAML\XML\SchemaValidatableElementTrait;
use SimpleSAML\XMLSchema\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSchema\Exception\TooManyElementsException;
use SimpleSAML\XMLSchema\Type\AnyURIValue;
use SimpleSAML\XMLSchema\Type\Base64BinaryValue;
use SimpleSAML\XMLSchema\Type\IDValue;
use SimpleSAML\XMLSchema\Type\StringValue;
use SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmInterface;
use SimpleSAML\XMLSecurity\Backend\OAEPParametersAware;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Key\KeyInterface;
use SimpleSAML\XMLSecurity\XML\ds\DigestMethod;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\xenc11\MGF;

use function array_last;
use function strval;

/**
 * Class representing an encrypted key.
 *
 * @package simplesamlphp/xml-security
 */
final class EncryptedKey extends AbstractEncryptedType implements SchemaValidatableElementInterface
{
    use SchemaValidatableElementTrait;


    /**
     * EncryptedKey constructor.
     *
     * @param \SimpleSAML\XMLSecurity\XML\xenc\CipherData $cipherData The CipherData object of this EncryptedData.
     * @param \SimpleSAML\XMLSchema\Type\IDValue|null $id
     *   The Id attribute of this object. Optional.
     * @param \SimpleSAML\XMLSchema\Type\AnyURIValue|null $type
     *   The Type attribute of this object. Optional.
     * @param \SimpleSAML\XMLSchema\Type\StringValue|null $mimeType
     *   The MimeType attribute of this object. Optional.
     * @param \SimpleSAML\XMLSchema\Type\AnyURIValue|null $encoding
     *   The Encoding attribute of this object. Optional.
     * @param \SimpleSAML\XMLSchema\Type\StringValue|null $recipient
     *   The Recipient attribute of this object. Optional.
     * @param \SimpleSAML\XMLSecurity\XML\xenc\CarriedKeyName|null $carriedKeyName
     *   The value of the CarriedKeyName element of this EncryptedData.
     * @param \SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod|null $encryptionMethod
     *   The EncryptionMethod object of this EncryptedData. Optional.
     * @param \SimpleSAML\XMLSecurity\XML\ds\KeyInfo|null $keyInfo The KeyInfo object of this EncryptedData. Optional.
     * @param \SimpleSAML\XMLSecurity\XML\xenc\ReferenceList|null $referenceList
     *   The ReferenceList object of this EncryptedData. Optional.
     */
    final public function __construct(
        CipherData $cipherData,
        ?IDValue $id = null,
        ?AnyURIValue $type = null,
        ?StringValue $mimeType = null,
        ?AnyURIValue $encoding = null,
        protected ?StringValue $recipient = null,
        protected ?CarriedKeyName $carriedKeyName = null,
        ?EncryptionMethod $encryptionMethod = null,
        ?KeyInfo $keyInfo = null,
        protected ?ReferenceList $referenceList = null,
    ) {
        parent::__construct($cipherData, $id, $type, $mimeType, $encoding, $encryptionMethod, $keyInfo);
    }


    /**
     * Get the value of the CarriedKeyName property.
     *
     * @return \SimpleSAML\XMLSecurity\XML\xenc\CarriedKeyName|null
     */
    public function getCarriedKeyName(): ?CarriedKeyName
    {
        return $this->carriedKeyName;
    }


    /**
     * Get the value of the Recipient attribute.
     *
     * @return \SimpleSAML\XMLSchema\Type\StringValue|null
     */
    public function getRecipient(): ?StringValue
    {
        return $this->recipient;
    }


    /**
     * Get the ReferenceList object.
     *
     * @return \SimpleSAML\XMLSecurity\XML\xenc\ReferenceList|null
     */
    public function getReferenceList(): ?ReferenceList
    {
        return $this->referenceList;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmInterface $decryptor The decryptor to use
     * to decrypt the key.
     *
     * @return string The decrypted key.
     */
    public function decrypt(EncryptionAlgorithmInterface $decryptor): string
    {
        $cipherValue =  $this->getCipherData()->getCipherValue();
        Assert::notNull(
            $cipherValue,
            'Decrypting keys by reference is not supported.',
            InvalidArgumentException::class,
        );

        Assert::eq(
            $decryptor->getAlgorithmId(),
            $this->getEncryptionMethod()?->getAlgorithm(),
            'Decryptor algorithm does not match algorithm used.',
            InvalidArgumentException::class,
        );

        if ($decryptor instanceof OAEPParametersAware) {
            $encryptionMethod = $this->getEncryptionMethod();
            $digestAlg = null;
            $mgf = null;

            if ($encryptionMethod !== null) {
                foreach ($encryptionMethod->getElements() as $child) {
                    if ($child instanceof DigestMethod) {
                        $digestAlg = $child->getAlgorithm()->getValue();
                    } elseif ($child instanceof MGF) {
                        $mgf = $child->getAlgorithm()->getValue();
                    } elseif ($child instanceof Chunk) {
                        if ($child->getNamespaceURI() === C::NS_XDSIG && $child->getLocalName() === 'DigestMethod') {
                            $digestAlg = self::getChunkAlgorithm($child);
                        } elseif ($child->getNamespaceURI() === C::NS_XENC11 && $child->getLocalName() === 'MGF') {
                            $mgf = self::getChunkAlgorithm($child);
                        }
                    }
                }
            }

            $decryptor->setOAEPParams($digestAlg, $mgf);
        }

        return $decryptor->decrypt(base64_decode($cipherValue->getContent()->getValue(), true));
    }


    /**
     * Read the 'Algorithm' attribute directly off an unregistered DigestMethod/MGF Chunk,
     * so XML-declared OAEP parameters are honoured even when the xml-common element
     * registry has not registered the typed ds\DigestMethod / xenc11\MGF classes.
     */
    private static function getChunkAlgorithm(Chunk $chunk): ?string
    {
        $xml = $chunk->getXML();
        return $xml->hasAttribute('Algorithm') ? $xml->getAttribute('Algorithm') : null;
    }


    /**
     * Create an EncryptedKey by encrypting a given key.
     *
     * @param \SimpleSAML\XMLSecurity\Key\KeyInterface $keyToEncrypt The key to encrypt.
     * @param \SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmInterface $encryptor The encryptor to use.
     * @param \SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod $encryptionMethod
     *   The EncryptionMethod object of this EncryptedData. Optional.
     * @param \SimpleSAML\XMLSchema\Type\IDValue|null $id The Id attribute of this object. Optional.
     * @param \SimpleSAML\XMLSchema\Type\AnyURIValue|null $type The Type attribute of this object. Optional.
     * @param \SimpleSAML\XMLSchema\Type\StringValue|null $mimeType
     *   The MimeType attribute of this object. Optional.
     * @param \SimpleSAML\XMLSchema\Type\AnyURIValue|null $encoding
     *   The Encoding attribute of this object. Optional.
     * @param \SimpleSAML\XMLSchema\Type\StringValue|null $recipient
     *   The Recipient attribute of this object. Optional.
     * @param \SimpleSAML\XMLSecurity\XML\xenc\CarriedKeyName|null $carriedKeyName
     *   The value of the CarriedKeyName element of this EncryptedData.
     * @param \SimpleSAML\XMLSecurity\XML\ds\KeyInfo|null $keyInfo The KeyInfo object of this EncryptedData. Optional.
     * @param \SimpleSAML\XMLSecurity\XML\xenc\ReferenceList|null $referenceList
     *   The ReferenceList object of this EncryptedData. Optional.
     *
     * @return \SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey The new EncryptedKey object.
     */
    public static function fromKey(
        KeyInterface $keyToEncrypt,
        EncryptionAlgorithmInterface $encryptor,
        EncryptionMethod $encryptionMethod,
        ?IDValue $id = null,
        ?AnyURIValue $type = null,
        ?StringValue $mimeType = null,
        ?AnyURIValue $encoding = null,
        ?StringValue $recipient = null,
        ?CarriedKeyName $carriedKeyName = null,
        ?KeyInfo $keyInfo = null,
        ?ReferenceList $referenceList = null,
    ): EncryptedKey {
        Assert::eq(
            $encryptor->getAlgorithmId(),
            $encryptionMethod->getAlgorithm()->getValue(),
            'Encryptor algorithm and encryption method do not match.',
            InvalidArgumentException::class,
        );

        return new self(
            new CipherData(
                new CipherValue(
                    Base64BinaryValue::fromString(base64_encode(
                        $encryptor->encrypt($keyToEncrypt->getMaterial()),
                    )),
                ),
            ),
            $id,
            $type,
            $mimeType,
            $encoding,
            $recipient,
            $carriedKeyName,
            $encryptionMethod,
            $keyInfo,
            $referenceList,
        );
    }


    /**
     * @inheritDoc
     *
     * @throws \SimpleSAML\XMLSchema\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(Dom\Element $xml): static
    {
        Assert::same($xml->localName, 'EncryptedKey', InvalidDOMElementException::class);
        Assert::same($xml->namespaceURI, EncryptedKey::NS, InvalidDOMElementException::class);

        $cipherData = CipherData::getChildrenOfClass($xml);
        Assert::count(
            $cipherData,
            1,
            'No or more than one CipherData element found in <xenc:EncryptedKey>.',
            TooManyElementsException::class,
        );

        $encryptionMethod = EncryptionMethod::getChildrenOfClass($xml);
        Assert::maxCount(
            $encryptionMethod,
            1,
            'No more than one EncryptionMethod element allowed in <xenc:EncryptedKey>.',
            TooManyElementsException::class,
        );

        $keyInfo = KeyInfo::getChildrenOfClass($xml);
        Assert::maxCount(
            $keyInfo,
            1,
            'No more than one KeyInfo element allowed in <xenc:EncryptedKey>.',
            TooManyElementsException::class,
        );

        $referenceLists = ReferenceList::getChildrenOfClass($xml);
        Assert::maxCount(
            $keyInfo,
            1,
            'Only one ReferenceList element allowed in <xenc:EncryptedKey>.',
            TooManyElementsException::class,
        );

        $carriedKeyNames = CarriedKeyName::getChildrenOfClass($xml);
        Assert::maxCount(
            $carriedKeyNames,
            1,
            'Only one CarriedKeyName element allowed in <xenc:EncryptedKey>.',
            TooManyElementsException::class,
        );

        return new static(
            $cipherData[0],
            self::getOptionalAttribute($xml, 'Id', IDValue::class, null),
            self::getOptionalAttribute($xml, 'Type', AnyURIValue::class, null),
            self::getOptionalAttribute($xml, 'MimeType', StringValue::class, null),
            self::getOptionalAttribute($xml, 'Encoding', AnyURIValue::class, null),
            self::getOptionalAttribute($xml, 'Recipient', StringValue::class, null),
            array_last($carriedKeyNames),
            array_last($encryptionMethod),
            array_last($keyInfo),
            array_last($referenceLists),
        );
    }


    /**
     * @inheritDoc
     */
    public function toXML(?Dom\Element $parent = null): Dom\Element
    {
        $e = parent::toXML($parent);

        if ($this->getRecipient() !== null) {
            $e->setAttribute('Recipient', strval($this->getRecipient()));
        }

        $this->getReferenceList()?->toXML($e);
        $this->getCarriedKeyName()?->toXML($e);

        return $e;
    }
}
