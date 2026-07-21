<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML\xenc;

use SimpleSAML\XMLSecurity\Alg\Encryption\EncryptionAlgorithmInterface;
use SimpleSAML\XMLSecurity\Backend\OAEPParametersAware;

/**
 * Combined interface for mocking a decryptor that is OAEPParametersAware.
 *
 * @internal
 * @package simplesamlphp/xml-security
 */
interface OaepAwareDecryptorInterface extends EncryptionAlgorithmInterface, OAEPParametersAware
{
}
