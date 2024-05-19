<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Adapter;

use Presta\BehatEvaluator\ExpressionLanguage\ExpressionLanguage;
use Presta\BehatEvaluator\ExpressionLanguage\ExpressionMatcher\FunctionExpressionMatcher;

final class EnumAdapter implements AdapterInterface
{
    public function __construct(private readonly ExpressionLanguage $expressionLanguage)
    {
    }

    public function __invoke(mixed $value): mixed
    {
        if (!\is_string($value)) {
            return $value;
        }

        $match = new FunctionExpressionMatcher();

        $expressions = $match('enum', $value);
        if ([] === $expressions) {
            return $value;
        }

        foreach ($expressions as $expression) {
            try {
                $evaluated = $this->expressionLanguage->evaluate($expression);
            } catch (\Throwable $exception) {
                if (\str_ends_with($exception->getMessage(), '" is not a valid enum.')) {
                    $value = "<$expression>";

                    continue;
                }

                throw $exception;
            }

            if ($evaluated instanceof \BackedEnum) {
                if ($value === "<$expression>") {
                    return $evaluated;
                }

                $evaluated = $evaluated->value;
            }

            if ($evaluated instanceof \UnitEnum) {
                if ($value === "<$expression>") {
                    return $evaluated;
                }

                throw new \RuntimeException("You can not get the \"value\" of a UnitEnum.");
            }

            // the expression is included in a larger string

            \assert(\is_int($evaluated) || \is_string($evaluated));

            $value = str_replace("<$expression>", (string)$evaluated, $value);
        }

        return match (\is_numeric($value)) {
            true => (int)$value,
            false => $value,
        };
    }
}
