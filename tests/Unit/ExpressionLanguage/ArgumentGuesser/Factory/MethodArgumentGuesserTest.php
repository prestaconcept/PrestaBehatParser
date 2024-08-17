<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit\ExpressionLanguage\ArgumentGuesser\Factory;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\ExpressionLanguage\ArgumentGuesser\Factory\ArgumentGuesserInterface;
use Presta\BehatEvaluator\ExpressionLanguage\ArgumentGuesser\Factory\MethodArgumentGuesser;

/**
 * @phpstan-import-type FactoryAttributes from ArgumentGuesserInterface
 */
final class MethodArgumentGuesserTest extends TestCase
{
    /**
     * @param FactoryAttributes|string|null $method
     * @param FactoryAttributes|string|null $min
     * @param FactoryAttributes|string|null $attributes
     */
    #[DataProvider('arguments')]
    public function testInvokingTheGuesser(
        string $expected,
        array|string|null $method,
        array|string|null $min,
        array|string|null $attributes,
        string|null $accessor,
    ): void {
        $guess = new MethodArgumentGuesser();

        self::assertSame($expected, $guess($method, $min, $attributes, $accessor));
    }

    /**
     * @return iterable<string, array{
     *     string,
     *     FactoryAttributes|string|null,
     *     FactoryAttributes|string|null,
     *     FactoryAttributes|string|null,
     *     string|null,
     * }>
     */
    public static function arguments(): iterable
    {
        yield 'all arguments set not null should return the default "find" method' => ['find', null, null, null, null];
        yield 'a string as 1st argument should return the 1st argument' => ['count', 'count', null, null, null];
    }
}
