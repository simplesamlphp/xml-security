<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\XML\xenc;

/**
 * A class implementing the xenc:EncryptionMethod element.
 *
 * @package simplesamlphp/xml-security
 */
class EncryptionMethod extends AbstractEncryptionMethod
{
    /**
     * EncryptionMethod constructor.
     *
     * @param string $algorithm
     * @param int|null $keySize
     * @param string|null $oaepParams
     * @param \SimpleSAML\XML\Chunk[] $children
     */
    public function __construct(
        string $algorithm,
        ?int $keySize = null,
        ?string $oaepParams = null,
        array $children = []
    ) {
        parent::__construct($algorithm, $keySize, $oaepParams, $children);
    }
}
