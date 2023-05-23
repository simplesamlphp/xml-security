<?php

declare(strict_types=1);

namespace SimpleSAML\XMLSecurity\Test\CryptoEncoding;

use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEM;
use SimpleSAML\XMLSecurity\CryptoEncoding\PEMBundle;
use SimpleSAML\XMLSecurity\Exception\IOException;
use UnexpectedValueException;

use function dirname;

/**
 * @group pem
 *
 * @internal
 */
class PEMBundleTest extends TestCase
{
    private static string $baseDir;


    /**
     */
    public static function setUpBeforeClass(): void
    {
        self::$baseDir = dirname(__FILE__, 3);
    }


    /**
     * @return \SimpleSAML\XMLSecurity\CryptoEncoding\PEMBundle
     */
    public function testBundle(): PEMBundle
    {
        $bundle = PEMBundle::fromFile(self::$baseDir . '/resources/certificates/cacert.pem');
        $this->assertInstanceOf(PEMBundle::class, $bundle);
        return $bundle;
    }


    /**
     * @depends testBundle
     *
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEMBundle $bundle
     */
    public function testAll(PEMBundle $bundle): void
    {
        $this->assertContainsOnlyInstancesOf(PEM::class, $bundle->all());
    }


    /**
     * @depends testBundle
     *
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEMBundle $bundle
     */
    public function testFirst(PEMBundle $bundle): void
    {
        $this->assertInstanceOf(PEM::class, $bundle->first());
        $this->assertEquals($bundle->all()[0], $bundle->first());
    }


    /**
     * @depends testBundle
     *
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEMBundle $bundle
     */
    public function testLast(PEMBundle $bundle): void
    {
        $this->assertInstanceOf(PEM::class, $bundle->last());
        $this->assertEquals($bundle->all()[149], $bundle->last());
    }


    /**
     * @depends testBundle
     *
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEMBundle $bundle
     */
    public function testCount(PEMBundle $bundle): void
    {
        $this->assertCount(150, $bundle);
    }


    /**
     * @depends testBundle
     *
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEMBundle $bundle
     */
    public function testIterator(PEMBundle $bundle): void
    {
        $values = [];
        foreach ($bundle as $pem) {
            $values[] = $pem;
        }
        $this->assertContainsOnlyInstancesOf(PEM::class, $values);
    }


    /**
     * @depends testBundle
     *
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEMBundle $bundle
     */
    public function testString(PEMBundle $bundle): void
    {
        $this->assertIsString($bundle->string());
    }


    /**
     * @depends testBundle
     *
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEMBundle $bundle
     */
    public function testToString(PEMBundle $bundle): void
    {
        $this->assertIsString(strval($bundle));
    }


    public function testInvalidPEM(): void
    {
        $this->expectException(UnexpectedValueException::class);
        PEMBundle::fromString('invalid');
    }


    public function testInvalidPEMData(): void
    {
        $str = <<<'DATA'
-----BEGIN TEST-----
%%%
-----END TEST-----
DATA;
        $this->expectException(UnexpectedValueException::class);
        PEMBundle::fromString($str);
    }


    public function testInvalidFile(): void
    {
        $this->expectException(IOException::class);
        PEMBundle::fromFile('/phpunit/some/nonexistent');
    }


    public function testFirstEmptyFail(): void
    {
        $bundle = new PEMBundle();
        $this->expectException(LogicException::class);
        $bundle->first();
    }


    public function testLastEmptyFail(): void
    {
        $bundle = new PEMBundle();
        $this->expectException(LogicException::class);
        $bundle->last();
    }


    /**
     * @depends testBundle
     *
     * @param \SimpleSAML\XMLSecurity\CryptoEncoding\PEMBundle $bundle
     */
    public function testWithPEMs(PEMBundle $bundle): void
    {
        $bundle = $bundle->withPEMs(new PEM('TEST', 'data'));
        $this->assertCount(151, $bundle);
    }
}
