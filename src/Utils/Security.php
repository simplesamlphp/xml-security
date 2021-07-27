<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Utils;

use DOMElement;
use Exception;
use RuntimeException;
use SimpleSAML\Assert\Assert;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\BlacklistedAlgorithmException;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\UnsupportedAlgorithmException;
use SimpleSAML\XMLSecurity\XMLSecEnc;
use SimpleSAML\XMLSecurity\XMLSecurityKey;

use function count;
use function hash_equals;
use function in_array;
use function openssl_pkey_get_details;
use function serialize;
use function sha1;
use function str_pad;
use function str_replace;
use function strlen;
use function strval;
use function substr;
use function trim;
use function var_export;

/**
 * A collection of security-related functions.
 *
 * @package simplesamlphp/xml-security
 */
class Security
{
    /**
     * Compare two strings in constant time.
     *
     * This function allows us to compare two given strings without any timing side channels
     * leaking information about them.
     *
     * @param string $known The reference string.
     * @param string $user The user-provided string to test.
     *
     * @return bool True if both strings are equal, false otherwise.
     */
    public static function compareStrings(string $known, string $user): bool
    {
        return hash_equals($known, $user);
    }


    /**
     * Compute the hash for some data with a given algorithm.
     *
     * @param string $alg The identifier of the algorithm to use.
     * @param string $data The data to digest.
     * @param bool $encode Whether to bas64-encode the result or not. Defaults to true.
     *
     * @return string The (binary or base64-encoded) digest corresponding to the given data.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If $alg is not a valid
     *   identifier of a supported digest algorithm.
     */
    public static function hash(string $alg, string $data, bool $encode = true): string
    {
        if (!array_key_exists($alg, C::$DIGEST_ALGORITHMS)) {
            throw new InvalidArgumentException('Unsupported digest method "' . $alg . '"');
        }

        $digest = hash(C::$DIGEST_ALGORITHMS[$alg], $data, true);
        if ($encode) {
            $digest = base64_encode($digest);
        }
        return $digest;
    }


    /**
     * Helper function to convert a XMLSecurityKey to the correct algorithm.
     *
     * @param \SimpleSAML\XMLSecurity\XMLSecurityKey $key The key.
     * @param string $algorithm The desired algorithm.
     * @param string $type Public or private key, defaults to public.
     * @return \SimpleSAML\XMLSecurity\XMLSecurityKey The new key.
     *
     * @throws \SimpleSAML\Assert\AssertionFailedException if assertions are false
     */
    public static function castKey(XMLSecurityKey $key, string $algorithm, string $type = null): XMLSecurityKey
    {
        $type = $type ?: 'public';
        Assert::oneOf($type, ["private", "public"]);

        // do nothing if algorithm is already the type of the key
        if ($key->type === $algorithm) {
            return $key;
        }

        if (!in_array($algorithm, array_keys(C::$RSA_DIGESTS, true))) {
            throw new UnsupportedAlgorithmException('Unsupported signing algorithm.');
        }

        /** @psalm-suppress PossiblyNullArgument */
        $keyInfo = openssl_pkey_get_details($key->key);
        if ($keyInfo === false) {
            throw new Exception('Unable to get key details from XMLSecurityKey.');
        }
        if (!isset($keyInfo['key'])) {
            throw new Exception('Missing key in public key details.');
        }

        $newKey = new XMLSecurityKey($algorithm, ['type' => $type]);
        $newKey->loadKey($keyInfo['key']);

        return $newKey;
    }


    /**
     * Decrypt an encrypted element.
     *
     * This is an internal helper function.
     *
     * @param \DOMElement $encryptedData The encrypted data.
     * @param \SimpleSAML\XMLSecurity\XMLSecurityKey $inputKey The decryption key.
     * @param array &$blacklist Blacklisted decryption algorithms.
     * @throws \Exception
     * @return \DOMElement The decrypted element.
     */
    private static function doDecryptElement(
        DOMElement $encryptedData,
        XMLSecurityKey $inputKey,
        array &$blacklist
    ): DOMElement {
        $enc = new XMLSecEnc();

        $enc->setNode($encryptedData);
        $enc->type = $encryptedData->getAttribute("Type");

        $symmetricKey = $enc->locateKey($encryptedData);
        if (!$symmetricKey) {
            throw new Exception('Could not locate key algorithm in encrypted data.');
        }

        $symmetricKeyInfo = $enc->locateKeyInfo($symmetricKey);
        if (!$symmetricKeyInfo) {
            throw new Exception('Could not locate <dsig:KeyInfo> for the encrypted key.');
        }

        $inputKeyAlgo = $inputKey->getAlgorithm();
        if ($symmetricKeyInfo->isEncrypted) {
            $symKeyInfoAlgo = $symmetricKeyInfo->getAlgorithm();

            Assert::true(
                !in_array($symKeyInfoAlgo, $blacklist, true),
                sprintf('Blacklisted algorithm;  \'%s\'.', $symKeyInfoAlgo),
                BlacklistedAlgorithmException::class
            );

            if ($symKeyInfoAlgo === C::KEY_TRANSPORT_OAEP_MGF1P && $inputKeyAlgo === C::KEY_TRANSPORT_RSA_1_5) {
                /*
                 * The RSA key formats are equal, so loading an RSA_1_5 key
                 * into an RSA_OAEP_MGF1P key can be done without problems.
                 * We therefore pretend that the input key is an
                 * RSA_OAEP_MGF1P key.
                 */
                $inputKeyAlgo = C::KEY_TRANSPORT_OAEP_MGF1P;
            }

            /* Make sure that the input key format is the same as the one used to encrypt the key. */
            if ($inputKeyAlgo !== $symKeyInfoAlgo) {
                throw new Exception(
                    'Algorithm mismatch between input key and key used to encrypt ' .
                    ' the symmetric key for the message. Key was: ' .
                    var_export($inputKeyAlgo, true) . '; message was: ' .
                    var_export($symKeyInfoAlgo, true)
                );
            }

            /** @var XMLSecEnc $encKey */
            $encKey = $symmetricKeyInfo->encryptedCtx;
            $symmetricKeyInfo->key = $inputKey->key;

            $keySize = $symmetricKey->getSymmetricKeySize();
            if ($keySize === null) {
                /* To protect against "key oracle" attacks, we need to be able to create a
                 * symmetric key, and for that we need to know the key size.
                 */
                throw new Exception(
                    'Unknown key size for encryption algorithm: ' . var_export($symmetricKey->type, true)
                );
            }

            try {
                /**
                 * @var string $key
                 * @psalm-suppress UndefinedClass
                 */
                $key = $encKey->decryptKey($symmetricKeyInfo);
                if (strlen($key) !== $keySize) {
                    throw new Exception(
                        'Unexpected key size (' . strval(strlen($key) * 8) . 'bits) for encryption algorithm: ' .
                        var_export($symmetricKey->type, true)
                    );
                }
            } catch (Exception $e) {
                /* We failed to decrypt this key. Log it, and substitute a "random" key. */
//                Utils::getContainer()->getLogger()->error('Failed to decrypt symmetric key: ' . $e->getMessage());
                /* Create a replacement key, so that it looks like we fail in the same way as if the key was correctly
                 * padded. */

                /* We base the symmetric key on the encrypted key and private key, so that we always behave the
                 * same way for a given input key.
                 */
                $encryptedKey = $encKey->getCipherValue();
                if ($encryptedKey === null) {
                    throw new Exception('No CipherValue available in the encrypted element.');
                }

                /** @psalm-suppress PossiblyNullArgument */
                $pkey = openssl_pkey_get_details($symmetricKeyInfo->key);
                $pkey = sha1(serialize($pkey), true);
                $key = sha1($encryptedKey . $pkey, true);

                /* Make sure that the key has the correct length. */
                if (strlen($key) > $keySize) {
                    $key = substr($key, 0, $keySize);
                } elseif (strlen($key) < $keySize) {
                    $key = str_pad($key, $keySize);
                }
            }
            $symmetricKey->loadkey($key);
        } else {
            $symKeyAlgo = $symmetricKey->getAlgorithm();
            /* Make sure that the input key has the correct format. */
            if ($inputKeyAlgo !== $symKeyAlgo) {
                throw new Exception(
                    'Algorithm mismatch between input key and key in message. ' .
                    'Key was: ' . var_export($inputKeyAlgo, true) . '; message was: ' .
                    var_export($symKeyAlgo, true)
                );
            }
            $symmetricKey = $inputKey;
        }

        $algorithm = $symmetricKey->getAlgorithm();
        if (in_array($algorithm, $blacklist, true)) {
            throw new BlacklistedAlgorithmException('Algorithm disabled: ' . var_export($algorithm, true));
        }

        /**
         * @var string $decrypted
         * @psalm-suppress UndefinedClass
         */
        $decrypted = $enc->decryptNode($symmetricKey, false);

        /*
         * This is a workaround for the case where only a subset of the XML
         * tree was serialized for encryption. In that case, we may miss the
         * namespaces needed to parse the XML.
         */
        $xml = '<root xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion" ' .
                        'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' .
            $decrypted . '</root>';

        try {
            $newDoc = DOMDocumentFactory::fromString($xml);
        } catch (RuntimeException $e) {
            throw new Exception('Failed to parse decrypted XML. Maybe the wrong sharedkey was used?', 0, $e);
        }

        /** @psalm-suppress PossiblyNullPropertyFetch */
        $decryptedElement = $newDoc->firstChild->firstChild;
        if (!($decryptedElement instanceof DOMElement)) {
            throw new Exception('Missing decrypted element or it was not actually a DOMElement.');
        }

        return $decryptedElement;
    }


    /**
     * Decrypt an encrypted element.
     *
     * @param \DOMElement $encryptedData The encrypted data.
     * @param \SimpleSAML\XMLSecurity\XMLSecurityKey $inputKey The decryption key.
     * @param array $blacklist Blacklisted decryption algorithms.
     * @throws \Exception
     * @return \DOMElement The decrypted element.
     */
    public static function decryptElement(
        DOMElement $encryptedData,
        XMLSecurityKey $inputKey,
        array $blacklist = []
    ): DOMElement {
        try {
            return self::doDecryptElement($encryptedData, $inputKey, $blacklist);
        } catch (Exception $e) {
            /*
             * Something went wrong during decryption, but for security
             * reasons we cannot tell the user what failed.
             */
//            Utils::getContainer()->getLogger()->error('Decryption failed: ' . $e->getMessage());
            throw new Exception('Failed to decrypt XML element.', 0, $e);
        }
    }
}
