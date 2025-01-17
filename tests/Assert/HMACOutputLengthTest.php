<?php

declare(strict_types=1);

namespace SimpleSAML\Test\XMLSecurity\Assert;

use PHPUnit\Framework\Attributes\{CoversClass, DataProvider};
use PHPUnit\Framework\TestCase;
use SimpleSAML\Assert\AssertionFailedException;
use SimpleSAML\XMLSecurity\Assert\Assert;

/**
 * Class \SimpleSAML\Test\XMLSecurity\Assert\HMACOutputLengthTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(Assert::class)]
final class HMACOutputLengthTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $HMACOutputLength
     */
    #[DataProvider('provideHMACOutputLength')]
    public function testValidHMACOutputLength(bool $shouldPass, string $HMACOutputLength): void
    {
        try {
            Assert::validHMACOutputLength($HMACOutputLength);
            $this->assertTrue($shouldPass);
        } catch (AssertionFailedException $e) {
            $this->assertFalse($shouldPass);
        }
    }


    /**
     * @return array<string, array{0: bool, 1: string}>
     */
    public static function provideHMACOutputLength(): array
    {
        return [
            'empty' => [false, ''],
            'valid positive integer' => [true, '128'],
            // Indivisible by 8 is caught by the type-class, because schema-wise it's perfectly valid
            'valid indivisible by 8' => [true, '4'],
            'invalid signed positive integer' => [false, '+128'],
            'invalid zero' => [false, '0'],
            'invalid leading zeros' => [false, '0000000000000000000128'],
            'invalid with fractional' => [false, '1.'],
            'invalid negative' => [false, '-128'],
            'invalid with thousands-delimiter' => [false, '1,28'],
        ];
    }
}
