<?php

declare(strict_types=1);

namespace SimpleSAML\Test\XMLSecurity\Type;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSecurity\Type\ECPointValue;

/**
 * Class \SimpleSAML\Test\XMLSecurity\Type\ECPointValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(ECPointValue::class)]
final class ECPointValueTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $ECPointValue
     */
    #[DataProvider('provideECPointValue')]
    public function testECPointValue(bool $shouldPass, string $ECPointValue): void
    {
        try {
            ECPointValue::fromString($ECPointValue);
            $this->assertTrue($shouldPass);
        } catch (SchemaViolationException $e) {
            $this->assertFalse($shouldPass);
        }
    }


    /**
     * @return array<string, array{0: bool, 1: string}>
     */
    public static function provideECPointValue(): array
    {
        return [
            'empty' => [false, ''],
            'valid' => [true, 'U2ltcGxlU0FNTHBocA=='],
            'illegal characters' => [false, '&*$(#&^@!(^%$'],
            'length not dividable by 4' => [false, 'U2ltcGxlU0FTHBocA=='],
        ];
    }
}
