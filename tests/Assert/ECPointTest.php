<?php

declare(strict_types=1);

namespace SimpleSAML\Test\XMLSecurity\Assert;

use PHPUnit\Framework\Attributes\{CoversClass, DataProvider};
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\XMLSecurity\Assert\Assert;

/**
 * Class \SimpleSAML\Test\XMLSecurity\Assert\ECPointTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(Assert::class)]
final class ECPointTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $ecPoint
     */
    #[DataProvider('provideECPoint')]
    public function testValidECPoint(bool $shouldPass, string $ecPoint): void
    {
        try {
            Assert::validECPoint($ecPoint);
            $this->assertTrue($shouldPass);
        } catch (AssertionFailedException $e) {
            $this->assertFalse($shouldPass);
        }
    }


    /**
     * @return array<string, array{0: bool, 1: string}>
     */
    public static function provideECPoint(): array
    {
        return [
            'empty' => [false, ''],
            'valid' => [true, 'U2ltcGxlU0FNTHBocA=='],
            'illegal characters' => [false, '&*$(#&^@!(^%$'],
            'length not dividable by 4' => [false, 'U2ltcGxlU0FTHBocA=='],
        ];
    }
}
