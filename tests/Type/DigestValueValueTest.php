<?php

declare(strict_types=1);

namespace SimpleSAML\Test\XMLSecurity\Type;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Exception\SchemaViolationException;
use SimpleSAML\XMLSecurity\Type\DigestValueValue;

/**
 * Class \SimpleSAML\Test\XMLSecurity\Type\DigestValueValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(DigestValueValue::class)]
final class DigestValueValueTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $digestValue
     */
    #[DataProvider('provideDigestValue')]
    public function testDigestValue(bool $shouldPass, string $digestValue): void
    {
        try {
            DigestValueValue::fromString($digestValue);
            $this->assertTrue($shouldPass);
        } catch (SchemaViolationException $e) {
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