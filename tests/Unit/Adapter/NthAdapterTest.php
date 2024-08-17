<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\Adapter\NthAdapter;
use Presta\BehatEvaluator\Tests\Resources\UnsupportedValuesProvider;

final class NthAdapterTest extends TestCase
{
    use UnsupportedValuesProvider;

    #[DataProvider('values')]
    public function testInvokingTheAdapter(mixed $expected, mixed $value): void
    {
        $evaluate = new NthAdapter();

        self::assertSame($expected, $evaluate($value));
    }

    /**
     * @return iterable<string, array{mixed, mixed}>
     */
    public static function values(): iterable
    {
        yield 'a number followed by "st" should return the numerical part as int' => [1, '1st'];
        yield 'a number followed by "nd" should return the numerical part as int' => [2, '2nd'];
        yield 'a number followed by "rd" should return the numerical part as int' => [3, '3rd'];
        yield 'a number followed by "th" should return the numerical part as int' => [10, '10th'];
        yield 'a number not followed by a "nth" expression should return the original string unchanged' => [
            '5cm',
            '5cm',
        ];
        yield 'a number followed by "th" inside a string should return the original string unchanged ' => [
            'the value 10th is skipped',
            'the value 10th is skipped',
        ];
        yield from self::unsupportedValues(['string']);
    }
}
