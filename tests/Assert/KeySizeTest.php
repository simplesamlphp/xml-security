<?php

declare(strict_types=1);

namespace SimpleSAML\Test\XMLSecurity\Assert;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\XMLSecurity\Assert\Assert;

/**
 * Class \SimpleSAML\Test\XMLSecurity\Assert\KeySizeTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(Assert::class)]
final class KeySizeTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $keySize
     */
    #[DataProvider('provideKeySize')]
    public function testValidKeySize(bool $shouldPass, string $keySize): void
    {
        try {
            Assert::validKeySize($keySize);
            $this->assertTrue($shouldPass);
        } catch (AssertionFailedException $e) {
            $this->assertFalse($shouldPass);
        }
    }


    /**
     * @return array<string, array{0: bool, 1: string}>
     */
    public static function provideKeySize(): array
    {
        return [
            'empty' => [false, ''],
            'valid positive integer' => [true, '123456'],
            'invalid signed positive integer' => [false, '+123456'],
            'invalid zero' => [false, '0'],
            'invalid leading zeros' => [false, '0000000000000000000005'],
            'invalid with fractional' => [false, '1.'],
            'invalid negative' => [false, '-1234'],
            'invalid with thousands-delimiter' => [false, '1,234'],
        ];
    }
}
