<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\Adapter\ScalarAdapter;
use Presta\BehatEvaluator\Tests\Resources\UnsupportedValuesProvider;

final class ScalarAdapterTest extends TestCase
{
    use UnsupportedValuesProvider;

    #[DataProvider('values')]
    public function testInvokingTheAdapter(mixed $expected, mixed $value): void
    {
        $evaluate = new ScalarAdapter();

        self::assertSame($expected, $evaluate($value));
    }

    /**
     * @return iterable<string, array{mixed, mixed}>
     */
    public static function values(): iterable
    {
        yield 'the string "null" should return null' => [null, 'null'];
        yield 'the string "true" should return true' => [true, 'true'];
        yield 'the string "false" should return false' => [false, 'false'];
        yield 'a string containing only a numeric float should return the float value of the string' => [5.5, '5.5'];
        yield 'a string containing only a numeric int should return the int value of the string' => [5, '5'];
        yield 'a string should return the same string' => ['this is a string', 'this is a string'];
        yield 'an empty string should return the same empty string' => ['', ''];
        yield from self::unsupportedNonScalarValues();
    }
}
