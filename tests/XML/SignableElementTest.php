<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XML\TestUtils\SerializableElementTestTrait;
use SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory;
use SimpleSAML\XMLSecurity\Constants as C;
use SimpleSAML\XMLSecurity\Exception\RuntimeException;
use SimpleSAML\XMLSecurity\Key\PrivateKey;
use SimpleSAML\XMLSecurity\TestUtils\PEMCertificatesMock;
use SimpleSAML\XMLSecurity\XML\ds\KeyInfo;
use SimpleSAML\XMLSecurity\XML\ds\X509Certificate;
use SimpleSAML\XMLSecurity\XML\ds\X509Data;

use function array_pop;
use function array_shift;
use function dirname;
use function explode;
use function file_get_contents;
use function join;
use function strval;
use function trim;

/**
 * Class \SimpleSAML\XMLSecurity\Test\XML\SignableElementTest
 *
 * @covers \SimpleSAML\XMLSecurity\Test\XML\CustomSignable
 *
 * @package simplesamlphp/xml-security
 */
final class SignableElementTest extends TestCase
{
    use SerializableElementTestTrait;

    /** @var string */
    private static string $certificate;

    /** @var \SimpleSAML\XMLSecurity\Key\PrivateKey */
    private static PrivateKey $key;

    /** @var \DOMDocument */
    private static DOMDocument $signed;


    /**
     */
    public function setUp(): void
    {
        self::$testedClass = CustomSignable::class;

        self::$xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 2) . '/resources/xml/custom_CustomSignable.xml',
        );

        self::$signed = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 2) . '/resources/xml/custom_CustomSignableSigned.xml',
        );

        $certificate = PEMCertificatesMock::loadPlainCertificateFile(PEMCertificatesMock::SELFSIGNED_CERTIFICATE);
        $certificateLines = explode("\n", trim($certificate));
        array_pop($certificateLines);
        array_shift($certificateLines);
        self::$certificate = join("\n", $certificateLines);

        self::$key = PEMCertificatesMock::getPrivateKey(PEMCertificatesMock::SELFSIGNED_PRIVATE_KEY);
    }


    /**
     * Test that signing produces the expected output.
     *
     * In this test we try to sign an entire document, since the element is the root of it, and doesn't have an ID.
     */
    public function testMarshalling(): void
    {
        $customSignable = CustomSignable::fromXML(self::$xmlRepresentation->documentElement);
        $this->assertFalse($customSignable->isEmptyElement());

        $factory = new SignatureAlgorithmFactory();
        $signer = $factory->getAlgorithm(C::SIG_RSA_SHA256, self::$key);

        $keyInfo = new KeyInfo([
            new X509Data([
                new X509Certificate(self::$certificate),
            ]),
        ]);

        $customSignable->sign($signer, C::C14N_EXCLUSIVE_WITHOUT_COMMENTS, $keyInfo);

        $this->assertEquals(
            self::$signed->saveXML(self::$signed->documentElement),
            strval($customSignable),
        );
    }


    /**
     * Test that signing an element works.
     *
     * This test implies signing an element. Since the element itself has an ID, we use that to create our reference.
     */
    public function testSigningElement(): void
    {
        $xml = DOMDocumentFactory::fromString(
            '<ssp:CustomSignable xmlns:ssp="urn:x-simplesamlphp:namespace" id="_1234"><ssp:Chunk><!--comment-->Some' .
            '<!--comment--></ssp:Chunk></ssp:CustomSignable>',
        );
        $customSignable = CustomSignable::fromXML($xml->documentElement);
        $this->assertFalse($customSignable->isEmptyElement());

        $factory = new SignatureAlgorithmFactory();
        $signer = $factory->getAlgorithm(C::SIG_RSA_SHA256, self::$key);

        $keyInfo = new KeyInfo([
            new X509Data([
                new X509Certificate(self::$certificate),
            ]),
        ]);

        $customSignable->sign($signer, C::C14N_EXCLUSIVE_WITHOUT_COMMENTS, $keyInfo);
        $signed = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 2) . '/resources/xml/custom_CustomSignableSignedWithId.xml',
        );

        $this->assertEquals(
            $signed->saveXML($signed->documentElement),
            strval($customSignable),
        );
    }


    /**
     * Test that signing a document with comments works.
     *
     * This tests attempts to sign a document with comments, and verifies that the resulting reference is an xpointer
     * pointing to the root of the document.
     */
    public function testSigningDocumentWithComments(): void
    {
        $xml = DOMDocumentFactory::fromString(
            '<ssp:CustomSignable xmlns:ssp="urn:x-simplesamlphp:namespace"><ssp:Chunk><!--comment-->Some' .
            '<!--comment--></ssp:Chunk></ssp:CustomSignable>',
        );
        $customSignable = CustomSignable::fromXML($xml->documentElement);
        $this->assertFalse($customSignable->isEmptyElement());

        $factory = new SignatureAlgorithmFactory();
        $signer = $factory->getAlgorithm(C::SIG_RSA_SHA256, self::$key);

        $keyInfo = new KeyInfo([
            new X509Data([
                new X509Certificate(self::$certificate),
            ]),
        ]);

        $customSignable->sign($signer, C::C14N_EXCLUSIVE_WITH_COMMENTS, $keyInfo);
        $signed = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 2) . '/resources/xml/custom_CustomSignableSignedWithComments.xml',
        );

        $this->assertEquals(
            $signed->saveXML($signed->documentElement),
            strval($customSignable),
        );
    }


    /**
     * Test that signing an element with an ID including comments works.
     *
     * This test attempts to sign an element with an ID, using exclusive canonicalization with comments. The resulting
     * reference should be an xpointer specifying the ID of the element.
     */
    public function testSigningElementWithIdAndComments(): void
    {
        $xml = DOMDocumentFactory::fromString(
            '<ssp:CustomSignable xmlns:ssp="urn:x-simplesamlphp:namespace" id="_1234"><ssp:Chunk><!--comment-->Some' .
            '<!--comment--></ssp:Chunk></ssp:CustomSignable>',
        );
        $customSignable = CustomSignable::fromXML($xml->documentElement);
        $this->assertFalse($customSignable->isEmptyElement());

        $factory = new SignatureAlgorithmFactory();
        $signer = $factory->getAlgorithm(C::SIG_RSA_SHA256, self::$key);

        $keyInfo = new KeyInfo([
            new X509Data([
                new X509Certificate(self::$certificate),
            ]),
        ]);

        $customSignable->sign($signer, C::C14N_EXCLUSIVE_WITH_COMMENTS, $keyInfo);
        $signed = DOMDocumentFactory::fromFile(
            dirname(__FILE__, 2) . '/resources/xml/custom_CustomSignableSignedWithCommentsAndId.xml'
        );

        $this->assertEquals(
            $signed->saveXML($signed->documentElement),
            strval($customSignable),
        );
    }


    /**
     * Test that signing an object with a document reference fails if there's no document root.
     *
     * This test attempts to sign a document with an element without an ID that's not marked as its root. This should
     * fail since we cannot use a self-document reference (because the element is not the root), and we don't have an
     * ID for the element, so we have no way to refer to it.
     */
    public function testSigningDocumentWithoutRoot(): void
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $node = $doc->importNode(self::$xmlRepresentation->documentElement, true);
        $customSignable = CustomSignable::fromXML($node);
        $factory = new SignatureAlgorithmFactory();
        $signer = $factory->getAlgorithm(C::SIG_RSA_SHA256, self::$key);
        $customSignable->sign($signer);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot create a document reference without a root element in the document.');
        $customSignable->toXML();
    }


    /**
     * Test that signing an object with a document reference fails if the object is not the document's root.
     *
     * This test attempts to sign an element without an ID, forcing us to use a self-document reference. However, the
     * document contains another element before the one we try to sign, and that other element is marked as the root
     * of the document. We cannot therefore create the self-document reference because the element we try to sign is
     * not the root, and we should fail accordingly.
     */
    public function testSigningWithDifferentRoot(): void
    {
        $doc = DOMDocumentFactory::fromString('<ns:Root><ns:foo>bar</ns:foo></ns:Root>');
        $node = $doc->importNode(self::$xmlRepresentation->documentElement, true);
        $doc->appendChild($node);
        $customSignable = CustomSignable::fromXML($node);
        $factory = new SignatureAlgorithmFactory();
        $signer = $factory->getAlgorithm(C::SIG_RSA_SHA256, self::$key);
        $customSignable->sign($signer);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Cannot create a document reference when signing an object that is not the root of the document. Please ' .
            'give your object an identifier.',
        );
        $customSignable->toXML($doc->documentElement);
    }
}
