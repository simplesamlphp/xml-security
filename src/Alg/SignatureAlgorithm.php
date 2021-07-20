<?php

namespace SimpleSAML\XMLSecurity\Alg;

use SimpleSAML\XMLSecurity\Backend\SignatureBackend;

/**
 * An interface representing algorithms that can be used for digital signatures.
 *
 * @package SimpleSAML\XMLSecurity\Alg
 */
interface SignatureAlgorithm
{
    /**
     * Get an array with all the identifiers for algorithms supported.
     *
     * @return string[]
     */
    public static function getSupportedAlgorithms(): array;


    /**
     * Get the digest used by this signature algorithm.
     *
     * @return string The identifier of the digest algorithm used.
     */
    public function getDigest(): string;


    /**
     * Get the identifier of this signature algorithm.
     *
     * @return string The identifier of this signature algorithm.
     */
    public function getAlgorithmId(): string;



    /**
     * Set the backend to use for actual computations by this algorithm.
     *
     * @param \SimpleSAML\XMLSecurity\Backend\SignatureBackend $backend The backend to use.
     *
     */
    public function setBackend(SignatureBackend $backend): void;


    /**
     * Sign a given plaintext with this cipher and the loaded key.
     *
     * @param string $plaintext The original text to sign.
     *
     * @return string|false The (binary) signature corresponding to the given plaintext.
     */
    public function sign(string $plaintext);


    /**
     * Verify a signature with this cipher and the loaded key.
     *
     * @param string $plaintext The original signed text.
     * @param string $signature The (binary) signature to verify.
     *
     * @return boolean True if the signature can be verified, false otherwise.
     */
    public function verify(string $plaintext, string $signature): bool;
}
