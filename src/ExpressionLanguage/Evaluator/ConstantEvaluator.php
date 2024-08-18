<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\ExpressionLanguage\Evaluator;

final class ConstantEvaluator
{
    /**
     * @param array<string, mixed> $arguments
     */
    public function __invoke(array $arguments, string $value): mixed
    {
        try {
            return \constant($value);
        } catch (\Throwable) {
        }

        return $value;
    }
}
