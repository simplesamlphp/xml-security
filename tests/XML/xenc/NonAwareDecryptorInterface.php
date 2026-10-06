<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmInterface;

/**
 * Interface for mocking a decryptor that is NOT OAEPParametersAware.
 *
 * @internal
 * @package simplesamlphp/xml-security
 */
interface NonAwareDecryptorInterface extends EncryptionAlgorithmInterface
{
}
