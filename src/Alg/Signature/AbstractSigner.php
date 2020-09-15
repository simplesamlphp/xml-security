<?php

namespace SimpleSAML\XMLSecurity\Alg\Signature;

use SimpleSAML\XMLSecurity\Alg\SignatureAlgorithm;
use SimpleSAML\XMLSecurity\Backend\SignatureBackend;
use SimpleSAML\XMLSecurity\Key\AbstractKey;

/**
 * An abstract class that implements a generic digital signature algorithm.
 *
 * @package SimpleSAML\XMLSecurity\\Alg\Signature
 */
abstract class AbstractSigner implements SignatureAlgorithm
{
    /** @var \SimpleSAML\XMLSecurity\Key\AbstractKey */
    protected AbstractKey $key;

    /** @var \SimpleSAML\XMLSecurity\Backend\SignatureBackend */
    protected SignatureBackend $backend;

    /** @var string */
    protected string $default_backend;

    /** @var string */
    protected string $digest;


    /**
     * Build a signature algorithm.
     *
     * @param \SimpleSAML\XMLSecurity\Key\AbstractKey $key The signing key.
     * @param string $digest The identifier of the digest algorithm to use.
     */
    public function __construct(AbstractKey $key, string $digest)
    {
        $this->key = $key;
        $this->digest = $digest;
        $this->backend = new $this->default_backend();
        $this->backend->setDigestAlg($digest);
    }


    /**
     * @return string
     */
    public function getDigest(): string
    {
        return $this->digest;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\Backend\SignatureBackend
     *
     */
    public function setBackend(SignatureBackend $backend): void
    {
        $this->backend = $backend;
        $this->backend->setDigestAlg($this->digest);
    }


    /**
     * Sign a given plaintext with the current algorithm and key.
     *
     * @param string $plaintext The plaintext to sign.
     *
     * @return string The (binary) signature corresponding to the given plaintext.
     */
    public function sign(string $plaintext): string
    {
        return $this->backend->sign($this->key, $plaintext);
    }


    /**
     * Verify a signature with the current algorithm and key.
     *
     * @param string $plaintext The original signed text.
     * @param string $signature The (binary) signature to verify.
     *
     * @return boolean True if the signature can be verified, false otherwise.
     */
    public function verify(string $plaintext, string $signature): bool
    {
        return $this->backend->verify($this->key, $plaintext, $signature);
    }
}
