<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity;

/**
 * A collection of constants used in this library, as defined by the XMLSec set of recommendations.
 *
 * @package simplesamlphp/xml-security
 */
class Constants extends \SimpleSAML\XML\Constants
{
    /**
     * Symmetric key wrap algorithms
     */
    public const KEY_WRAP_3DES = 'http://www.w3.org/2001/04/xmlenc#kw-tripledes';
    public const KEY_WRAP_AES128 = 'http://www.w3.org/2001/04/xmlenc#kw-aes128';
    public const KEY_WRAP_AES192 = 'http://www.w3.org/2001/04/xmlenc#kw-aes192';
    public const KEY_WRAP_AES256 = 'http://www.w3.org/2001/04/xmlenc#kw-aes256';

    /** @var string[] */
    public static array $KEY_WRAP_ALGORITHMS = [
        self::KEY_WRAP_3DES,
        self::KEY_WRAP_AES128,
        self::KEY_WRAP_AES192,
        self::KEY_WRAP_AES256,
    ];


    /**
     * Key derivation algorithms
     */
    public const KEY_DERIVATION_CONCATKDF = 'http://www.w3.org/2009/xmlenc11#ConcatKDF';
    public const KEY_DERIVATION_PBKDF2 = 'http://www.w3.org/2009/xmlenc11#pbkdf2';

    /** @var string[] */
    public static array $KEY_DERIVATION_ALGORITHMS = [
        self::KEY_DERIVATION_CONCATKDF,
        self::KEY_DERIVATION_PBKDF2,
    ];


    /**
     * Key agreement algorithms
     */
    public const KEY_AGREEMENT_ECDH_ES = 'http://www.w3.org/2009/xmlenc11#ECDH-ES';
    public const KEY_AGREEMENT_DH = 'http://www.w3.org/2001/04/xmlenc#dh';
    public const KEY_AGREEMENT_DH_ES = 'http://www.w3.org/2009/xmlenc11#dh-es';

    /** @var string[] */
    public static array $KEY_AGREEMENT_ALGORITHMS = [
        self::KEY_AGREEMENT_ECDH_ES,
        self::KEY_AGREEMENT_DH,
        self::KEY_AGREEMENT_DH_ES,
    ];


    /**
     * Message digest algorithms
     */
    public const DIGEST_SHA1 = 'http://www.w3.org/2000/09/xmldsig#sha1';
    public const DIGEST_SHA224 = 'http://www.w3.org/2001/04/xmldsig-more#sha224';
    public const DIGEST_SHA256 = 'http://www.w3.org/2001/04/xmlenc#sha256';
    public const DIGEST_SHA384 = 'http://www.w3.org/2001/04/xmldsig-more#sha384';
    public const DIGEST_SHA512 = 'http://www.w3.org/2001/04/xmlenc#sha512';
    public const DIGEST_RIPEMD160 = 'http://www.w3.org/2001/04/xmlenc#ripemd160';

    /** @var array<string, string> */
    public static array $DIGEST_ALGORITHMS = [
        self::DIGEST_SHA1 => 'sha1',
        self::DIGEST_SHA224 => 'sha224',
        self::DIGEST_SHA256 => 'sha256',
        self::DIGEST_SHA384 => 'sha384',
        self::DIGEST_SHA512 => 'sha512',
        self::DIGEST_RIPEMD160 => 'ripemd160',
    ];


    /**
     * Padding schemas
     */
    public const PADDING_PKCS1 = "PKCS1";
    public const PADDING_PKCS1_OAEP = "OAEP";


    /**
     * Block encryption algorithms
     */
    public const BLOCK_ENC_3DES = 'http://www.w3.org/2001/04/xmlenc#tripledes-cbc';
    public const BLOCK_ENC_AES128 = 'http://www.w3.org/2001/04/xmlenc#aes128-cbc';
    public const BLOCK_ENC_AES192 = 'http://www.w3.org/2001/04/xmlenc#aes192-cbc';
    public const BLOCK_ENC_AES256 = 'http://www.w3.org/2001/04/xmlenc#aes256-cbc';
    public const BLOCK_ENC_AES128_GCM = 'http://www.w3.org/2009/xmlenc11#aes128-gcm';
    public const BLOCK_ENC_AES192_GCM = 'http://www.w3.org/2009/xmlenc11#aes192-gcm';
    public const BLOCK_ENC_AES256_GCM = 'http://www.w3.org/2009xmlenc11#aes256-gcm';

    /** @var array<string, string> */
    public static array $BLOCK_CIPHER_ALGORITHMS = [
        self::BLOCK_ENC_3DES => 'des-ede3-cbc',
        self::BLOCK_ENC_AES128 => 'aes-128-cbc',
        self::BLOCK_ENC_AES192 => 'aes-192-cbc',
        self::BLOCK_ENC_AES256 => 'aes-256-cbc',
        self::BLOCK_ENC_AES128_GCM => 'aes-128-gcm',
        self::BLOCK_ENC_AES192_GCM => 'aes-192-gcm',
        self::BLOCK_ENC_AES256_GCM => 'aes-256-gcm',
    ];

    /** @var array<string, positive-int> */
    public static array $BLOCK_SIZES = [
        self::BLOCK_ENC_3DES => 8,
        self::BLOCK_ENC_AES128 => 16,
        self::BLOCK_ENC_AES192 => 16,
        self::BLOCK_ENC_AES256 => 16,
        self::BLOCK_ENC_AES128_GCM => 16,
        self::BLOCK_ENC_AES192_GCM => 16,
        self::BLOCK_ENC_AES256_GCM => 16,
    ];

    /** @var array<string, positive-int> */
    public static array $BLOCK_CIPHER_KEY_SIZES = [
        self::BLOCK_ENC_3DES => 24,
        self::BLOCK_ENC_AES128 => 16,
        self::BLOCK_ENC_AES192 => 24,
        self::BLOCK_ENC_AES256 => 32,
        self::BLOCK_ENC_AES128_GCM => 16,
        self::BLOCK_ENC_AES192_GCM => 24,
        self::BLOCK_ENC_AES256_GCM => 32,
    ];


    /**
     * Key transport algorithms
     */
    public const KEY_TRANSPORT_RSA_1_5 = 'http://www.w3.org/2001/04/xmlenc#rsa-1_5';
    public const KEY_TRANSPORT_OAEP = 'http://www.w3.org/2009/xmlenc11#rsa-oaep';
    public const KEY_TRANSPORT_OAEP_MGF1P = 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p';

    /** @var string[] */
    public static array $KEY_TRANSPORT_ALGORITHMS = [
        self::KEY_TRANSPORT_RSA_1_5,
        self::KEY_TRANSPORT_OAEP,
        self::KEY_TRANSPORT_OAEP_MGF1P,
    ];


    /**
     * Canonicalization algorithms
     */
    public const C14N_INCLUSIVE_WITH_COMMENTS = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315#WithComments';
    public const C14N_INCLUSIVE_WITHOUT_COMMENTS = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
    public const C14N_EXCLUSIVE_WITH_COMMENTS = 'http://www.w3.org/2001/10/xml-exc-c14n#WithComments';
    public const C14N_EXCLUSIVE_WITHOUT_COMMENTS = 'http://www.w3.org/2001/10/xml-exc-c14n#';
    public const C14N11_INCLUSIVE_WITH_COMMENTS = 'http://www.w3.org/2006/12/xml-c14n11';
    public const C14N11_INCLUSIVE_WITHOUT_COMMENTS = 'http://www.w3.org/2006/12/xml-c14n11#WithComments';

    /** @var string[] */
    public static array $CANONICALIZATION_ALGORITHMS = [
        self::C14N_INCLUSIVE_WITH_COMMENTS,
        self::C14N_INCLUSIVE_WITHOUT_COMMENTS,
        self::C14N_EXCLUSIVE_WITH_COMMENTS,
        self::C14N_EXCLUSIVE_WITHOUT_COMMENTS,
//        self::C14N11_INCLUSIVE_WITH_COMMENTS,
//        self::C14N11_INCLUSIVE_WITHOUT_COMMENTS,
    ];


    /**
     * Signature algorithms
     */
    public const SIG_RSA_SHA1 = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
    public const SIG_RSA_SHA224 = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha224';
    public const SIG_RSA_SHA256 = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256';
    public const SIG_RSA_SHA384 = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha384';
    public const SIG_RSA_SHA512 = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha512';
    public const SIG_RSA_RIPEMD160 = 'http://www.w3.org/2001/04/xmldsig-more#rsa-ripemd160';
    public const SIG_HMAC_SHA1 = 'http://www.w3.org/2000/09/xmldsig#hmac-sha1';
    public const SIG_HMAC_SHA224 = 'http://www.w3.org/2001/04/xmldsig-more#hmac-sha224';
    public const SIG_HMAC_SHA256 = 'http://www.w3.org/2001/04/xmldsig-more#hmac-sha256';
    public const SIG_HMAC_SHA384 = 'http://www.w3.org/2001/04/xmldsig-more#hmac-sha384';
    public const SIG_HMAC_SHA512 = 'http://www.w3.org/2001/04/xmldsig-more#hmac-sha512';
    public const SIG_HMAC_RIPEMD160 = 'http://www.w3.org/2001/04/xmldsig-more#hmac-ripemd160';

    /** @var array<string, string> */
    public static array $RSA_DIGESTS = [
        self::SIG_RSA_SHA1 => self::DIGEST_SHA1,
        self::SIG_RSA_SHA224 => self::DIGEST_SHA224,
        self::SIG_RSA_SHA256 => self::DIGEST_SHA256,
        self::SIG_RSA_SHA384 => self::DIGEST_SHA384,
        self::SIG_RSA_SHA512 => self::DIGEST_SHA512,
        self::SIG_RSA_RIPEMD160 => self::DIGEST_RIPEMD160,
    ];

    /** @var array<string, string> */
    public static array $HMAC_DIGESTS = [
        self::SIG_HMAC_SHA1 => self::DIGEST_SHA1,
        self::SIG_HMAC_SHA224 => self::DIGEST_SHA224,
        self::SIG_HMAC_SHA256 => self::DIGEST_SHA256,
        self::SIG_HMAC_SHA384 => self::DIGEST_SHA384,
        self::SIG_HMAC_SHA512 => self::DIGEST_SHA512,
        self::SIG_HMAC_RIPEMD160 => self::DIGEST_RIPEMD160,
    ];


    /**
     * Encoding algorithms
     */
    public const ENCODING_BASE64 = 'http://www.w3.org/2000/09/xmldsig#base64';


    /**
     * Transforms algorithms
     */
    public const TRANSFORMS_BASE64 = 'http://www.w3.org/2000/09/xmldsig#base64';


    /**
     * XML & XPath namespaces and identifiers
     */
    public const NS_XDSIG = 'http://www.w3.org/2000/09/xmldsig#';
    public const NS_XDSIG11 = 'http://www.w3.org/2009/xmldsig11#';

    public const XMLDSIG_ENVELOPED = 'http://www.w3.org/2000/09/xmldsig#enveloped-signature';
    public const XMLDSIG_MANIFEST = 'http://www.w3.org/2000/09/xmldsig#Manifest';

    public const XMLDSIG11_DER_ENCODED_KEY_VALUE = 'https://www.w3.org/2009/xmldsig11#DEREncodedKeyValue';

    public const NS_XENC = 'http://www.w3.org/2001/04/xmlenc#';
    public const NS_XENC11 = 'http://www.w3.org/2009/xmlenc11#';
    public const XMLENC_CONTENT = 'http://www.w3.org/2001/04/xmlenc#Content';
    public const XMLENC_ELEMENT = 'http://www.w3.org/2001/04/xmlenc#Element';
    public const XMLENC_ENCRYPTEDKEY = 'http://www.w3.org/2001/04/xmlenc#EncryptedKey';
    public const XMLENC_EXI = 'http://www.w3.org/2009/xmlenc11#EXI';
}
