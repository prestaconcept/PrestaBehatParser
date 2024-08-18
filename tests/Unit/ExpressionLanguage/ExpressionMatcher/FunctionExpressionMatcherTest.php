<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit\ExpressionLanguage\ExpressionMatcher;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\ExpressionLanguage\ExpressionMatcher\FunctionExpressionMatcher;

final class FunctionExpressionMatcherTest extends TestCase
{
    /**
     * @param array<string> $expected
     */
    #[DataProvider('strings')]
    public function testMatchingAllExpressions(array $expected, string $name, string $text): void
    {
        $match = new FunctionExpressionMatcher();

        self::assertSame($expected, $match($name, $text));
    }

    /**
     * @return iterable<string, array{array<string>, string}>
     */
    public static function strings(): iterable
    {
        $simpleFunction = 'foobar()';
        $functionWithArguments = 'foobar("foo", "bar")';
        $functionWithDelimiters = 'foobar("(<foo", "bar>)")';

        yield 'a simple function expression should return the simple function' => [
            [$simpleFunction],
            'foobar',
            "<$simpleFunction>",
        ];
        yield 'a function expression containing arguments should return the function and it\'s arguments' => [
            [$functionWithArguments],
            'foobar',
            "<$functionWithArguments>",
        ];
        yield 'a function expression containing delimiters should return the function including the delimiters' => [
            [$functionWithDelimiters],
            'foobar',
            "<$functionWithDelimiters>",
        ];
        yield 'a simple function expression inside a larger string should return the simple function' => [
            [$simpleFunction],
            'foobar',
            "The expression <$simpleFunction> is inside a string",
        ];
        yield 'a function expression containing arguments inside a larger string'
        . ' should return the function and it\'s arguments' => [
            [$functionWithArguments],
            'foobar',
            "The expression <$functionWithArguments> is inside a string",
        ];
        yield 'a function expression containing delimiters inside a larger string'
        . ' should return the function including the delimiters' => [
            [$functionWithDelimiters],
            'foobar',
            "The expression <$functionWithDelimiters> is inside a string",
        ];
        yield 'many function expressions inside a string should return the functions' => [
            [$simpleFunction, $functionWithArguments, $functionWithDelimiters],
            'foobar',
            "The expressions <$simpleFunction>, <$functionWithArguments> and <$functionWithDelimiters>"
                . ' are inside a string',
        ];
        yield 'a simple function expression and a name being a pattern'
        . ' should return the simple function whose name matches the pattern' => [
            ['foo()'],
            'foo(_bar)?',
            '<foo()>',
        ];
    }
}
