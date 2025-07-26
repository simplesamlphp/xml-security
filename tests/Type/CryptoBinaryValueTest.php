<?php

declare(strict_types=1);

namespace SimpleSAML\Test\XMLSecurity\Type;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSecurity\Type\CryptoBinaryValue;

/**
 * Class \SimpleSAML\Test\XMLSecurity\Type\CryptoBinaryValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(CryptoBinaryValue::class)]
final class CryptoBinaryValueTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $cryptoBinary
     */
    #[DataProvider('provideCryptoBinary')]
    public function testCryptoBinary(bool $shouldPass, string $cryptoBinary): void
    {
        try {
            CryptoBinaryValue::fromString($cryptoBinary);
            $this->assertTrue($shouldPass);
        } catch (SchemaViolationException $e) {
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
