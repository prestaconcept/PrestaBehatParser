<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit\ExpressionLanguage\ArgumentGuesser\Factory;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\ExpressionLanguage\ArgumentGuesser\Factory\AccessorArgumentGuesser;
use Presta\BehatEvaluator\ExpressionLanguage\ArgumentGuesser\Factory\ArgumentGuesserInterface;

/**
 * @phpstan-import-type FactoryAttributes from ArgumentGuesserInterface
 */
final class AccessorArgumentGuesserTest extends TestCase
{
    /**
     * @param FactoryAttributes|string|null $expected
     * @param FactoryAttributes|string|null $method
     * @param FactoryAttributes|string|null $min
     * @param FactoryAttributes|string|null $attributes
     */
    #[DataProvider('arguments')]
    public function testInvokingTheGuesser(
        array|string|null $expected,
        array|string|null $method,
        array|string|null $min,
        array|string|null $attributes,
        string|null $accessor,
    ): void {
        $guess = new AccessorArgumentGuesser();

        self::assertSame($expected, $guess($method, $min, $attributes, $accessor));
    }

    /**
     * @return iterable<string, array{
     *     FactoryAttributes|string|null,
     *     FactoryAttributes|string|null,
     *     FactoryAttributes|string|null,
     *     FactoryAttributes|string|null,
     *     string|null,
     * }>
     */
    public static function arguments(): iterable
    {
        yield 'all arguments set not null should return null' => [null, null, null, null, null];
        yield 'a non null method and a string as 2nd argument should return the 2nd argument' => [
            'firstname',
            'find',
            'firstname',
            null,
            null,
        ];
        yield 'a non null method, an array of attributes and a string as 3rd argument'
            . ' should return the 3rd argument' => [
            'lastname',
            'find',
            [],
            'lastname',
            null,
        ];
        yield 'a non null method, an numeric value as 2nd argument and a string as 3rd argument'
            . ' should return the 3rd argument' => [
            'lastname',
            'find',
            '1',
            'lastname',
            null,
        ];
        yield 'a non null method, an numeric value as 2nd argument, an array of attributes and a string as 4th argument'
            . ' should return the 4th argument' => [
            'lastname',
            'find',
            '1',
            [],
            'lastname',
        ];
    }
}
