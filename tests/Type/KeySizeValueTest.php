<?php

declare(strict_types=1);

namespace SimpleSAML\Test\XMLSecurity\Type;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XMLSecurity\Type\KeySizeValue;

/**
 * Class \SimpleSAML\Test\XMLSecurity\Type\KeySizeValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(KeySizeValue::class)]
final class KeySizeValueTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $keySize
     */
    #[DataProvider('provideKeySize')]
    public function testKeySize(bool $shouldPass, string $keySize): void
    {
        try {
            KeySizeValue::fromString($keySize);
            $this->assertTrue($shouldPass);
        } catch (SchemaViolationException $e) {
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
            'valid with whitespace collapse' => [true, " 1 234 \n"],
        ];
    }
}
