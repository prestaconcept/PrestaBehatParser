<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit\Builder;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\Adapter\AdapterInterface;
use Presta\BehatEvaluator\EvaluatorBuilder;

final class EvaluatorBuilderTest extends TestCase
{
    public function testSettingOptions(): void
    {
        $culture = 'fr_FR';
        $factoryNamespace = 'Presta\\BehatEvaluator\\Tests\\Application\\Foundry\\Factory\\';
        $inflector = InflectorFactory::create()->build();

        $adapter = new class($culture, $factoryNamespace, $inflector) implements AdapterInterface {
            public function __construct(
                private readonly string $culture,
                private readonly string $factoryNamespace,
                private readonly Inflector $inflector,
            ) {
            }

            /**
             * @return array{culture: string, factory_namespace: string, inflector: Inflector}
             */
            public function __invoke(mixed $value): array
            {
                return [
                    'culture' => $this->culture,
                    'factory_namespace' => $this->factoryNamespace,
                    'inflector' => $this->inflector,
                ];
            }
        };

        $builder = new EvaluatorBuilder();
        $builder->withCulture($culture);
        $builder->withFactoryNamespace($factoryNamespace);
        $builder->withInflector($inflector);
        $builder->registerAdapterFactory($adapter::class, static fn (): AdapterInterface => $adapter);

        $evaluate = $builder->build();

        self::assertSame(
            [
                'culture' => $culture,
                'factory_namespace' => $factoryNamespace,
                'inflector' => $inflector,
            ],
            $evaluate('foo'),
        );
    }
}
