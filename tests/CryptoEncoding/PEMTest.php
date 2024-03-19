<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\CryptoEncoding;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;
use UnexpectedValueException;

use function base64_encode;
use function dirname;
use function file_get_contents;

/**
 * @internal
 */
#[Group('pem')]
class PEMTest extends TestCase
{
    private static string $baseDir;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$baseDir = dirname(__FILE__, 3);
    }


    public function testFromString(): void
    {
        $str = file_get_contents(self::$baseDir . '/resources/keys/pubkey.pem');
        $pem = PEM::fromString($str);
        $this->assertInstanceOf(PEM::class, $pem);
    }


    /**
     * @return \SimpleSAML\XMLSecurity\CryptoEncoding\PEM
     */
    public function testFromFile(): PEM
    {
        $pem = PEM::fromFile(self::$baseDir . '/resources/keys/pubkey.pem');
        $this->assertInstanceOf(PEM::class, $pem);
        return $pem;
    }


    /**
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEM $pem
     */
    #[Depends('testFromFile')]
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
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEM $pem
     */
    #[Depends('testFromFile')]
    public function testString(PEM $pem): void
    {
        $this->assertIsString($pem->string());
    }


    /**
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEM $pem
     */
    #[Depends('testFromFile')]
    public function testToString(PEM $pem): void
    {
        $this->assertIsString(strval($pem));
    }
}
