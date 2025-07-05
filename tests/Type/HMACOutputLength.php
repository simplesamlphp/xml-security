<?php

declare(strict_types=1);

namespace SimpleSAML\Test\XMLSecurity\Type;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XMLSchema\Exception\SchemaViolationException;
use SimpleSAML\XMLSecurity\Type\HMACOutputLengthValue;

/**
 * Class \SimpleSAML\Test\XMLSecurity\Type\HMACOutputLengthValueTest
 *
 * @package simplesamlphp/xml-security
 */
#[CoversClass(HMACOutputLengthValue::class)]
final class HMACOutputLengthValueTest extends TestCase
{
    /**
     * @param boolean $shouldPass
     * @param string $HMACOutputLength
     */
    #[DataProvider('provideHMACOutputLength')]
    public function testHMACOutputLength(bool $shouldPass, string $HMACOutputLength): void
    {
        try {
            HMACOutputLengthValue::fromString($HMACOutputLength);
            $this->assertTrue($shouldPass);
        } catch (SchemaViolationException $e) {
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
            'invalid indivisible by 8' => [false, '4'],
            'invalid signed positive integer' => [false, '+128'],
            'invalid zero' => [false, '0'],
            'invalid leading zeros' => [false, '0000000000000000000128'],
            'invalid with fractional' => [false, '1.'],
            'invalid negative' => [false, '-128'],
            'invalid with thousands-delimiter' => [false, '1,28'],
            'valid with whitespace collapse' => [true, " 1 28\n"],
        ];
    }
}
