<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML;

use DOMElement;
use Exception;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\AbstractXMLElement;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedData;
use SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey;
use SimpleSAML\XMLSecurity\XMLSecEnc;
use SimpleSAML\XMLSecurity\XMLSecurityKey;

/**
 * Trait aggregating functionality for encrypted elements.
 *
 * @package simplesamlphp/xml-security
 */
trait EncryptedElementTrait
{
    /**
     * The current encrypted ID.
     *
     * @var \SimpleSAML\XMLSecurity\XML\xenc\EncryptedData
     * @psalm-suppress PropertyNotSetInConstructor
     */
    protected EncryptedData $encryptedData;

    /**
     * A list of encrypted keys.
     *
     * @var \SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey[]
     */
    protected array $encryptedKeys = [];


    /**
     * Constructor for encrypted elements.
     *
     * @param \SimpleSAML\XMLSecurity\XML\xenc\EncryptedData $encryptedData The EncryptedData object.
     * @param \SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey[] $encryptedKeys
     *   An array of zero or more EncryptedKey objects.
     */
    public function __construct(EncryptedData $encryptedData, array $encryptedKeys)
    {
        $this->setEncryptedData($encryptedData);
        $this->setEncryptedKeys($encryptedKeys);
    }


    /**
     * Get the EncryptedData object.
     *
     * @return \SimpleSAML\XMLSecurity\XML\xenc\EncryptedData
     */
    public function getEncryptedData(): EncryptedData
    {
        return $this->encryptedData;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\XML\xenc\EncryptedData $encryptedData
     */
    protected function setEncryptedData(EncryptedData $encryptedData): void
    {
        $this->encryptedData = $encryptedData;
    }


    /**
     * Get the array of EncryptedKey objects
     *
     * @return \SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey[]
     */
    public function getEncryptedKeys(): array
    {
        return $this->encryptedKeys;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\XML\xenc\EncryptedKey[] $encryptedKeys
     */
    protected function setEncryptedKeys(array $encryptedKeys): void
    {
        Assert::allIsInstanceOf(
            $encryptedKeys,
            EncryptedKey::class,
            'All encrypted keys in <' . $this->getQualifiedName() . '> must be an instance of EncryptedKey.'
        );

        $this->encryptedKeys = $encryptedKeys;
    }


    /**
     * Create an encrypted element from a given unencrypted element and a key.
     *
     * @param \SimpleSAML\XML\AbstractXMLElement $element
     * @param \SimpleSAML\XMLSecurity\XMLSecurityKey $key
     *
     * @return \SimpleSAML\XMLSecurity\XML\EncryptedElementInterface
     * @throws \Exception
     */
    public static function fromUnencryptedElement(
        AbstractXMLElement $element,
        XMLSecurityKey $key
    ): EncryptedElementInterface {
        $xml = $element->toXML();

        $enc = new XMLSecEnc();
        $enc->setNode($xml);
        $enc->type = XMLSecEnc::Element;

        switch ($key->type) {
            case C::BLOCK_ENC_3DES:
            case C::BLOCK_ENC_AES128:
            case C::BLOCK_ENC_AES192:
            case C::BLOCK_ENC_AES256:
            case C::BLOCK_ENC_AES128_GCM:
            case C::BLOCK_ENC_AES192_GCM:
            case C::BLOCK_ENC_AES256_GCM:
                $symmetricKey = $key;
                break;

            case C::KEY_TRANSPORT_RSA_1_5:
            case C::SIG_RSA_SHA1:
            case C::SIG_RSA_SHA224:
            case C::SIG_RSA_SHA256:
            case C::SIG_RSA_SHA384:
            case C::SIG_RSA_SHA512:
            case C::KEY_TRANSPORT_OAEP:
            case C::KEY_TRANSPORT_OAEP_MGF1P:
                $symmetricKey = new XMLSecurityKey(C::BLOCK_ENC_AES128);
                $symmetricKey->generateSessionKey();

                $enc->encryptKey($key, $symmetricKey);

                break;

            default:
                throw new Exception('Unknown key type for encryption: ' . $key->type);
        }

        $dom = $enc->encryptNode($symmetricKey);
        /** @var \SimpleSAML\XMLSecurity\XML\xenc\EncryptedData $encData */
        $encData = EncryptedData::fromXML($dom);
        return new static($encData, []);
    }


    /**
     * @inheritDoc
     * @return \SimpleSAML\XMLSecurity\XML\EncryptedElementInterface
     *
     * @throws \SimpleSAML\XML\Exception\InvalidDOMElementException
     *   If the qualified name of the supplied element is wrong
     */
    public static function fromXML(DOMElement $xml): object
    {
        Assert::same(
            $xml->localName,
            AbstractXMLElement::getClassName(static::class),
            InvalidDOMElementException::class
        );
        Assert::same($xml->namespaceURI, static::NS, InvalidDOMElementException::class);

        $ed = EncryptedData::getChildrenOfClass($xml);
        Assert::count($ed, 1, 'No more or less than one EncryptedData element allowed in ' .
            AbstractXMLElement::getClassName(static::class) . '.');

        $ek = EncryptedKey::getChildrenOfClass($xml);

        return new static($ed[0], $ek);
    }


    /**
     * @inheritDoc
     */
    public function toXML(DOMElement $parent = null): DOMElement
    {
        $e = $this->instantiateParentElement($parent);

        $this->encryptedData->toXML($e);

        foreach ($this->encryptedKeys as $key) {
            $key->toXML($e);
        }

        return $e;
    }


    abstract public function instantiateParentElement(DOMElement $parent = null): DOMElement;


    abstract public function getQualifiedName(): string;
}
