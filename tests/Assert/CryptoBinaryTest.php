<?php

declare(strict_types=1);

namespace SimpleSAML\Test\XMLSecurity\Assert;

use PHPUnit\Framework\Attributes\{CoversClass, DataProvider};
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\XMLSecurity\Assert\Assert;

/**
 * Class \SimpleSAML\Test\XMLSecurity\Assert\CryptoBinaryTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(Assert::class)]
final class CryptoBinaryTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $cryptoBinary
     */
    #[DataProvider('provideCryptoBinary')]
    public function testValidCryptoBinary(bool $shouldPass, string $cryptoBinary): void
    {
        try {
            Assert::validCryptoBinary($cryptoBinary);
            $this->assertTrue($shouldPass);
        } catch (AssertionFailedException $e) {
            $this->assertFalse($shouldPass);
        }
    }


    /**
     * @return array<string, array{0: bool, 1: string}>
     */
    public static function provideCryptoBinary(): array
    {
        return [
            'empty' => [false, ''],
            'valid' => [true, 'U2ltcGxlU0FNTHBocA=='],
            'illegal characters' => [false, '&*$(#&^@!(^%$'],
            'length not dividable by 4' => [false, 'U2ltcGxlU0FTHBocA=='],
        ];
    }
}
