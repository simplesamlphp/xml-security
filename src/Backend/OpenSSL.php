<?php

namespace SimpleSAML\XMLSecurity\Backend;

use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\InvalidArgumentException;
use SimpleSAML\XMLSecurity\Exception\RuntimeException;
use SimpleSAML\XMLSecurity\Key\AbstractKey;
use SimpleSAML\XMLSecurity\Key\AsymmetricKey;
use SimpleSAML\XMLSecurity\Key\PrivateKey;
use SimpleSAML\XMLSecurity\Utils\Random;

use function chr;
use function mb_strlen;
use function openssl_cipher_iv_length;
use function openssl_decrypt;
use function openssl_encrypt;
use function openssl_error_string;
use function openssl_private_decrypt;
use function openssl_public_decrypt;
use function openssl_private_encrypt;
use function openssl_public_encrypt;
use function openssl_sign;
use function openssl_verify;
use function ord;
use function str_repeat;
use function substr;

/**
 * Backend for encryption and digital signatures based on the native openssl library.
 *
 * @package SimpleSAML\XMLSecurity\Backend
 */
final class OpenSSL implements EncryptionBackend, SignatureBackend
{
    // digital signature options
    /** @var string */
    protected string $digest;

    // asymmetric encryption options
    /** @var int */
    protected int $padding = C::PADDING_PKCS1;

    // symmetric encryption options
    /** @var string */
    protected string $cipher;

    /** @var int */
    protected int $blocksize;

    /** @var int */
    protected int $keysize;


    /**
     * Build a new OpenSSL backend.
     */
    public function __construct()
    {
        $this->setDigestAlg(C::DIGEST_SHA256);
        $this->setCipher(C::BLOCK_ENC_AES128_GCM);
    }


    /**
     * Encrypt a given plaintext with this cipher and a given key.
     *
     * @param \SimpleSAML\XMLSecurity\Key\AbstractKey $key The key to use to encrypt.
     * @param string $plaintext The original text to encrypt.
     *
     * @return string The encrypted plaintext (ciphertext).
     * @throws \SimpleSAML\XMLSecurity\Exception\RuntimeException If there is an error while encrypting the plaintext.
     */
    public function encrypt(AbstractKey $key, string $plaintext): string
    {
        if ($key instanceof AsymmetricKey) {
            // asymmetric encryption
            $fn = 'openssl_public_encrypt';
            if ($key instanceof PrivateKey) {
                $fn = 'openssl_private_encrypt';
            }

            $ciphertext = '';
            if (!$fn($plaintext, $ciphertext, $key->get(), $this->padding)) {
                throw new RuntimeException('Cannot encrypt data: ' . openssl_error_string());
            }
            return $ciphertext;
        }

        // symmetric encryption
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = Random::generateRandomBytes($ivlen);
        $plaintext = $this->pad($plaintext);
        $ciphertext = openssl_encrypt(
            $plaintext,
            $this->cipher,
            $key->get(),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );

        if (!$ciphertext) {
            throw new RuntimeException('Cannot encrypt data: ' . openssl_error_string());
        }
        return $iv . $ciphertext;
    }


    /**
     * Decrypt a given ciphertext with this cipher and a given key.
     *
     * @param \SimpleSAML\XMLSecurity\Key\AbstractKey $key The key to use to decrypt.
     * @param string $ciphertext The encrypted text to decrypt.
     *
     * @return string The decrypted ciphertext (plaintext).
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\RuntimeException If there is an error while decrypting the ciphertext.
     */
    public function decrypt(AbstractKey $key, string $ciphertext): string
    {
        if ($key instanceof AsymmetricKey) {
            // asymmetric encryption
            $fn = 'openssl_public_decrypt';
            if ($key instanceof PrivateKey) {
                $fn = 'openssl_private_decrypt';
            }

            $plaintext = '';
            if (!$fn($ciphertext, $plaintext, $key->get(), $this->padding)) {
                throw new RuntimeException('Cannot decrypt data: ' . openssl_error_string());
            }
            return $plaintext;
        }

        // symmetric encryption
        $ivlen = openssl_cipher_iv_length($this->cipher);
        $iv = substr($ciphertext, 0, $ivlen);
        $ciphertext = substr($ciphertext, $ivlen);

        $plaintext = openssl_decrypt(
            $ciphertext,
            $this->cipher,
            $key->get(),
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $iv
        );

        if ($plaintext === false) {
            throw new RuntimeException('Cannot decrypt data: ' . openssl_error_string());
        }
        return $this->unpad($plaintext);
    }


    /**
     * Sign a given plaintext with this cipher and a given key.
     *
     * @param \SimpleSAML\XMLSecurity\Key\AbstractKey $key The key to use to sign.
     * @param string $plaintext The original text to sign.
     *
     * @return string The (binary) signature corresponding to the given plaintext.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\RuntimeException If there is an error while signing the plaintext.
     */
    public function sign(AbstractKey $key, string $plaintext): string
    {
        if (!openssl_sign($plaintext, $signature, $key->get(), $this->digest)) {
            throw new RuntimeException('Cannot sign data: ' . openssl_error_string());
        }
        return $signature;
    }


    /**
     * Verify a signature with this cipher and a given key.
     *
     * @param \SimpleSAML\XMLSecurity\Key\AbstractKey $key The key to use to verify.
     * @param string $plaintext The original signed text.
     * @param string $signature The (binary) signature to verify.
     *
     * @return boolean True if the signature can be verified, false otherwise.
     */
    public function verify(AbstractKey $key, string $plaintext, string $signature): bool
    {
        return openssl_verify($plaintext, $signature, $key->get(), $this->digest) === 1;
    }


    /**
     * Set the cipher to be used by the backend.
     *
     * @param string $cipher The identifier of the cipher.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If the cipher is unknown or not supported.
     */
    public function setCipher(string $cipher): void
    {
        if (!isset(C::$BLOCK_CIPHER_ALGORITHMS[$cipher])) {
            throw new InvalidArgumentException('Invalid or unknown cipher');
        }
        $this->cipher = C::$BLOCK_CIPHER_ALGORITHMS[$cipher];
        $this->blocksize = C::$BLOCK_SIZES[$cipher];
        $this->keysize = C::$BLOCK_CIPHER_KEY_SIZES[$cipher];
    }


    /**
     * Set the digest algorithm to be used by this backend.
     *
     * @param string $digest The identifier of the digest algorithm.
     *
     * @throws \SimpleSAML\XMLSecurity\Exception\InvalidArgumentException If the given digest is not valid.
     */
    public function setDigestAlg(string $digest): void
    {
        if (!isset(C::$DIGEST_ALGORITHMS[$digest])) {
            throw new InvalidArgumentException('Unknown digest or non-cryptographic hash function.');
        }
        $this->digest = C::$DIGEST_ALGORITHMS[$digest];
    }


    /**
     * Pad a plaintext using ISO 10126 padding.
     *
     * @param string $plaintext The plaintext to pad.
     *
     * @return string The padded plaintext.
     */
    public function pad(string $plaintext): string
    {
        $padchr = $this->blocksize - (mb_strlen($plaintext) % $this->blocksize);
        $pattern = chr($padchr);
        return $plaintext . str_repeat($pattern, $padchr);
    }


    /**
     * Remove an existing ISO 10126 padding from a given plaintext.
     *
     * @param string $plaintext The padded plaintext.
     *
     * @return string The plaintext without the padding.
     */
    public function unpad(string $plaintext): string
    {
        return substr($plaintext, 0, -ord(substr($plaintext, -1)));
    }
}
