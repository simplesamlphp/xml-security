<?php

declare(strict_types=1);

namespace SimpleSAML\Test\XMLSecurity\Assert;

use PHPUnit\Framework\Attributes\{CoversClass, DataProvider};
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\XMLSecurity\Assert\Assert;

/**
 * Class \SimpleSAML\Test\XMLSecurity\Assert\DigestValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(Assert::class)]
final class DigestValueTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $digestValue
     */
    #[DataProvider('provideDigestValue')]
    public function testValidDigestValue(bool $shouldPass, string $digestValue): void
    {
        try {
            Assert::validDigestValue($digestValue);
            $this->assertTrue($shouldPass);
        } catch (AssertionFailedException $e) {
            $this->assertFalse($shouldPass);
        }
    }


    /**
     * @return array<string, array{0: bool, 1: string}>
     */
    public static function provideDigestValue(): array
    {
        return [
            'empty' => [false, ''],
            'valid' => [true, 'U2ltcGxlU0FNTHBocA=='],
            'illegal characters' => [false, '&*$(#&^@!(^%$'],
            'length not dividable by 4' => [false, 'U2ltcGxlU0FTHBocA=='],
        ];
    }
}
