<?php

declare(strict_types=1);

namespace Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use Presta\BehatEvaluator\Evaluator;
use Presta\BehatEvaluator\EvaluatorBuilder;
use Presta\BehatEvaluator\Tests\Application\Foundry\Factory\UserFactory;
use Presta\BehatEvaluator\Tests\Integration\KernelTestCase;
use Zenstruck\Foundry\Test\Factories;

final class EvaluatorTest extends KernelTestCase
{
    use Factories;

    #[DataProvider('values')]
    public function testRegisteringTheAdapterBeforeCallingTheStaticHelpers(mixed $expected, mixed $value): void
    {
        UserFactory::createOne(['firstname' => 'John', 'lastname' => 'Doe']);

        $builder = new EvaluatorBuilder();
        $builder->withFactoryNamespace('Presta\\BehatEvaluator\\Tests\\Application\\Foundry\\Factory\\');

        self::assertSame([$expected], Evaluator::evaluateMany([$value], $builder));
    }

    /**
     * @return iterable<string, array{mixed, mixed}>
     */
    public static function values(): iterable
    {
        yield 'a constant expression should not be necessary' => [
            ARRAY_FILTER_USE_KEY,
            '<constant("ARRAY_FILTER_USE_KEY")>',
        ];
        yield 'a datetime expression should not be necessary' => ['2023-01-01', '<datetime("2023-01-01", "Y-m-d")>'];
        yield 'a factory expression should not be necessary' => [1, '<factory("user", "count")>'];
        yield 'a nth expression should not be necessary' => [5, '5th'];
        yield 'a scalar expression should not be necessary' => [null, 'null'];
        yield 'an escaped expression should not be necessary' => [
            'The double quotes around "foo" should be unescaped',
            'The double quotes around \"foo\" should be unescaped',
        ];
    }
}
