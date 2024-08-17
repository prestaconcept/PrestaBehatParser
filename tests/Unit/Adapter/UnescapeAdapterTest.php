<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\Adapter\UnescapeAdapter;
use Presta\BehatEvaluator\Tests\Resources\UnsupportedValuesProvider;

final class UnescapeAdapterTest extends TestCase
{
    use UnsupportedValuesProvider;

    #[DataProvider('values')]
    public function testInvokingTheAdapter(mixed $expected, mixed $value): void
    {
        $evaluate = new UnescapeAdapter();

        self::assertSame($expected, $evaluate($value));
    }

    /**
     * @return iterable<string, array{mixed, mixed}>
     */
    public static function values(): iterable
    {
        yield 'a string containing escaped double quotes should return the string with unescaped double quotes' => [
            'The double quotes around "foo" should be unescaped',
            'The double quotes around \"foo\" should be unescaped',
        ];
        yield 'a string containing escaped single quotes should return the string with unescaped single quotes' => [
            "The double quotes around 'foo' should be unescaped",
            "The double quotes around \'foo\' should be unescaped",
        ];
        yield 'a string containing escaped backslashes should return the string with unescaped backslashes' => [
            'The backslash \\ should be unescaped',
            'The backslash \\\\ should be unescaped',
        ];
        yield 'a string containing no escaped character should return the same string' => [
            'this is a string',
            'this is a string',
        ];
        yield from self::unsupportedValues(['string']);
    }
}
