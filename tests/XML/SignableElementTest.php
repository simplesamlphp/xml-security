<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\XML;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\XML\SerializableXMLTestTrait;
use SimpleSAML\XML\DOMDocumentFactory;
use SimpleSAML\XMLSecurity\Alg\Signature\SignatureAlgorithmFactory;
use SimpleSAML\XMLSecurity\Constants;
use SimpleSAML\XMLSecurity\Key\PrivateKey;
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
    use SerializableXMLTestTrait;

    /** @var string */
    private string $certificate;

    /** @var PrivateKey */
    private PrivateKey $key;

    /** @var \DOMDocument */
    private \DOMDocument $signed;


    /**
     */
    public function setUp(): void
    {
        $this->testedClass = CustomSignable::class;

        $this->xmlRepresentation = DOMDocumentFactory::fromFile(
            dirname(dirname(__FILE__)) . '/resources/xml/custom_CustomSignable.xml'
        );

        $this->signed = DOMDocumentFactory::fromFile(
            dirname(dirname(__FILE__)) . '/resources/xml/custom_CustomSigned.xml'
        );

        $certificate = file_get_contents(
            dirname(dirname(__FILE__)) . '/resources/certificates/rsa-pem/selfsigned.simplesamlphp.org.crt'
        );
        $certificateLines = explode("\n", trim($certificate));
        array_pop($certificateLines);
        array_shift($certificateLines);
        $this->certificate = join("\n", $certificateLines);

        $this->key = PrivateKey::fromFile(
            dirname(dirname(__FILE__)) . '/resources/certificates/rsa-pem/selfsigned.simplesamlphp.org_nopasswd.key'
        );
    }


    /**
     * Test that signing produces the expected output.
     */
    public function testSigning(): void
    {
        $document = DOMDocumentFactory::fromString(
            '<ssp:Some>Chunk</ssp:Some>'
        );
        $customSignable = new CustomSignable($document->documentElement);
        $this->assertFalse($customSignable->isEmptyElement());

        $factory = new SignatureAlgorithmFactory();
        $signer = $factory->getAlgorithm(Constants::SIG_RSA_SHA256, $this->key);

        $keyInfo = new KeyInfo([
            new X509Data([
                new X509Certificate($this->certificate)
            ])
        ]);

        $customSignable->sign($signer, Constants::C14N_EXCLUSIVE_WITHOUT_COMMENTS, $keyInfo);

        $this->assertEquals(
            $this->signed->saveXML($this->signed->documentElement),
            strval($customSignable)
        );
    }
}

