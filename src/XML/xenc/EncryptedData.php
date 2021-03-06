<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

use DOMElement;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\Exception\InvalidDOMElementException;
use SimpleSAML\XML\Exception\MissingElementException;
use SimpleSAML\XML\Exception\TooManyElementsException;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;

/**
 * Class containing encrypted data.
 *
 * Note: <xenc:EncryptionProperties> elements are not supported.
 *
 * @package simplesamlphp/xml-security
 */
class EncryptedData extends AbstractEncryptedType
{
    /**
     * EncryptedData constructor.
     *
     * @param \SimpleSAML\XMLSecurity\XML\xenc\CipherData $cipherData The CipherData object of this EncryptedData.
     * @param string|null $id The Id attribute of this object. Optional.
     * @param string|null $type The Type attribute of this object. Optional.
     * @param string|null $mimeType The MimeType attribute of this object. Optional.
     * @param string|null $encoding The Encoding attribute of this object. Optional.
     * @param \SimpleSAML\XMLSecurity\XML\xenc\EncryptionMethod|null $encryptionMethod
     *   The EncryptionMethod object of this EncryptedData. Optional.
     * @param \SimpleSAML\XMLSecurity\XML\ds\KeyInfo|null $keyInfo The KeyInfo object of this EncryptedData. Optional.
     */
    public function __construct(
        CipherData $cipherData,
        ?string $id = null,
        ?string $type = null,
        ?string $mimeType = null,
        ?string $encoding = null,
        ?EncryptionMethod $encryptionMethod = null,
        ?KeyInfo $keyInfo = null
    ) {
        parent::__construct($cipherData, $id, $type, $mimeType, $encoding, $encryptionMethod, $keyInfo);
    }
}
