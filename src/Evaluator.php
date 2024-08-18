<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator;

use Presta\BehatEvaluator\Adapter\AdapterInterface;

final class Evaluator implements AdapterInterface
{
    /**
     * @param iterable<AdapterInterface> $adapters
     */
    public function __construct(private readonly iterable $adapters)
    {
    }

    public function __invoke(mixed $value): mixed
    {
        foreach ($this->adapters as $evaluate) {
            $value = $evaluate($value);
        }

        return $value;
    }

    public static function evaluate(mixed $value, EvaluatorBuilder $builder = new EvaluatorBuilder()): mixed
    {
        $evaluate = $builder->build();

        return $evaluate($value);
    }

    /**
     * @param array<mixed> $values
     *
     * @return array<mixed>
     */
    public static function evaluateMany(array $values, EvaluatorBuilder $builder = new EvaluatorBuilder()): array
    {
        return \array_map(
            static fn (mixed $value): mixed => self::evaluate($value, $builder),
            $values,
        );
    }
}
