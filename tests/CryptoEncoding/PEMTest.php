<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\CryptoEncoding;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;
use UnexpectedValueException;

use function base64_encode;
use function dirname;
use function file_get_contents;

/**
 * @group pem
 *
 * @internal
 */
class PEMTest extends TestCase
{
    private string $base_dir;


    /**
     */
    public function setUp(): void
    {
        $this->base_dir = dirname(__FILE__, 2);
    }


    public function testFromString(): void
    {
        $str = file_get_contents($this->base_dir . '/resources/keys/pubkey.pem');
        $pem = PEM::fromString($str);
        $this->assertInstanceOf(PEM::class, $pem);
    }


    /**
     * @return \SimpleSAML\XMLSecurity\CryptoEncoding\PEM
     */
    public function testFromFile(): PEM
    {
        $pem = PEM::fromFile($this->base_dir . '/resources/keys/pubkey.pem');
        $this->assertInstanceOf(PEM::class, $pem);
        return $pem;
    }


    /**
     * @depends testFromFile
     *
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEM $pem
     */
    public function testType(PEM $pem): void
    {
        $this->assertEquals(PEM::TYPE_PUBLIC_KEY, $pem->type());
    }


    public function testData(): void
    {
        $data = 'payload';
        $encoded = base64_encode($data);
        $str = <<<DATA
-----BEGIN TEST-----
{$encoded}
-----END TEST-----
DATA;
        $this->assertEquals($data, PEM::fromString($str)->data());
    }


    public function testInvalidPEM(): void
    {
        $this->expectException(UnexpectedValueException::class);
        PEM::fromString('invalid');
    }


    public function testInvalidPEMData(): void
    {
        $str = <<<'DATA'
-----BEGIN TEST-----
%%%
-----END TEST-----
DATA;
        $this->expectException(UnexpectedValueException::class);
        PEM::fromString($str);
    }


    public function testInvalidFile(): void
    {
        $this->expectException(RuntimeException::class);
        PEM::fromFile('/phpunit/some/nonexistent');
    }


    /**
     * @depends testFromFile
     *
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEM $pem
     */
    public function testString(PEM $pem): void
    {
        $this->assertIsString($pem->string());
    }


    /**
     * @depends testFromFile
     *
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEM $pem
     */
    public function testToString(PEM $pem): void
    {
        $this->assertIsString(strval($pem));
    }
}
