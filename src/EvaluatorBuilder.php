<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Presta\BehatEvaluator\Adapter\AdapterInterface;
use Presta\BehatEvaluator\Adapter\ConstantAdapter;
use Presta\BehatEvaluator\Adapter\DateTimeAdapter;
use Presta\BehatEvaluator\Adapter\EnumAdapter;
use Presta\BehatEvaluator\Adapter\FactoryAdapter;
use Presta\BehatEvaluator\Adapter\JsonAdapter;
use Presta\BehatEvaluator\Adapter\NthAdapter;
use Presta\BehatEvaluator\Adapter\ScalarAdapter;
use Presta\BehatEvaluator\Adapter\UnescapeAdapter;
use Presta\BehatEvaluator\ExpressionLanguage\ExpressionLanguage;
use Presta\BehatEvaluator\Foundry\FactoryClassFactory;

final class EvaluatorBuilder
{
    /**
     * @var array<class-string<AdapterInterface>, \Closure(ExpressionLanguage): AdapterInterface>
     */
    private array $adapterFactories = [];

    private string $culture = 'en_US';

    private string $factoryNamespace = 'App\\Foundry\\Factory';

    private Inflector $inflector;

    public function __construct()
    {
        $this->inflector = InflectorFactory::create()->build();
        $this->registerAdapterFactory(
            ConstantAdapter::class,
            static fn (ExpressionLanguage $expressionLanguage): AdapterInterface
                => new ConstantAdapter($expressionLanguage),
        );
        $this->registerAdapterFactory(
            DateTimeAdapter::class,
            static fn (ExpressionLanguage $expressionLanguage): AdapterInterface
                => new DateTimeAdapter($expressionLanguage),
        );
        $this->registerAdapterFactory(
            EnumAdapter::class,
            static fn (ExpressionLanguage $expressionLanguage): AdapterInterface
                => new EnumAdapter($expressionLanguage),
        );
        $this->registerAdapterFactory(
            FactoryAdapter::class,
            static fn (ExpressionLanguage $expressionLanguage): AdapterInterface
                => new FactoryAdapter($expressionLanguage),
        );
        $this->registerAdapterFactory(JsonAdapter::class, static fn (): AdapterInterface => new JsonAdapter());
        $this->registerAdapterFactory(NthAdapter::class, static fn (): AdapterInterface => new NthAdapter());
        $this->registerAdapterFactory(ScalarAdapter::class, static fn (): AdapterInterface => new ScalarAdapter());
        $this->registerAdapterFactory(UnescapeAdapter::class, static fn (): AdapterInterface => new UnescapeAdapter());
    }

    /**
     * @deprecated
     *
     * Use @see self::registerAdapterFactory() instead.
     */
    public function registerAdapter(AdapterInterface $adapter): self
    {
        $this->adapterFactories[$adapter::class] = static fn (): AdapterInterface => $adapter;

        return $this;
    }

    /**
     * @param class-string<AdapterInterface> $class
     * @param \Closure(ExpressionLanguage): AdapterInterface $factory
     */
    public function registerAdapterFactory(string $class, \Closure $factory): self
    {
        $this->adapterFactories[$class] = $factory;

        return $this;
    }

    /**
     * @param class-string<AdapterInterface> $class
     */
    public function unregisterAdapterFactory(string $class): self
    {
        unset($this->adapterFactories[$class]);

        return $this;
    }

    public function withCulture(string $culture): self
    {
        $this->culture = $culture;

        return $this;
    }

    public function withFactoryNamespace(string $factoryNamespace): self
    {
        $this->factoryNamespace = $factoryNamespace;

        return $this;
    }

    public function withInflector(Inflector $inflector): self
    {
        $this->inflector = $inflector;

        return $this;
    }

    public function build(): Evaluator
    {
        $expressionLanguage = new ExpressionLanguage(
            new FactoryClassFactory($this->factoryNamespace, $this->inflector),
            $this->culture,
        );

        return new Evaluator(
            \array_values(
                \array_map(
                    static fn (\Closure $factory): AdapterInterface => $factory($expressionLanguage),
                    $this->adapterFactories,
                ),
            ),
        );
    }
}
