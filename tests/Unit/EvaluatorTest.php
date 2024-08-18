<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\Adapter\AdapterInterface;
use Presta\BehatEvaluator\Evaluator;

final class EvaluatorTest extends TestCase
{
    #[DataProvider('values')]
    public function testInvokingTheEvaluator(mixed $expected, mixed $value): void
    {
        $adapters = [
            new class() implements AdapterInterface {
                public function __invoke(mixed $value): mixed
                {
                    if ('123' === $value) {
                        return 123;
                    }

                    return $value;
                }
            },
            new class() implements AdapterInterface {
                public function __invoke(mixed $value): mixed
                {
                    if (123 === $value) {
                        return true;
                    }

                    return $value;
                }
            },
        ];

        $evaluate = new Evaluator($adapters);

        self::assertSame($expected, $evaluate($value));
    }

    /**
     * @return iterable<string, array{mixed, mixed}>
     */
    public static function values(): iterable
    {
        yield 'the string "123" should trigger both adapters and return true' => [true, '123'];
        yield 'the value 123 should only trigger the second adapter and return true' => [true, 123];
        yield 'the string "null" should not trigger any adapters and therefore return the original "null" string'
            => ['null', 'null']
        ;
    }
}
