<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Adapter;

use Presta\BehatEvaluator\ExpressionLanguage\ExpressionLanguage;
use Presta\BehatEvaluator\ExpressionLanguage\ExpressionMatcher\FunctionExpressionMatcher;

/**
 * @example <factory("user", {"email": "john.doe@prestaconcept.net"})>
 * @example <factory("user", {"email": "john.doe@prestaconcept.net"}, "id")>
 * @example <factory("user", "find", {"email": "john.doe@prestaconcept.net"})>
 * @example <factory("user", "find", {"email": "john.doe@prestaconcept.net"}, "id")>
 * @example <factory("user/role", "count")>
 * @example <factory("rating number", "count")>
 */
final class FactoryAdapter implements AdapterInterface
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

        $expressions = $match('factory', $value);
        if ([] === $expressions) {
            return $value;
        }

        foreach ($expressions as $expression) {
            $evaluated = $this->expressionLanguage->evaluate($expression);
            if ("<$expression>" === $value) {
                return $evaluated;
            }

            if (!\is_scalar($evaluated) && !$evaluated instanceof \Stringable) {
                throw new \RuntimeException();
            }

            $value = str_replace("<$expression>", (string) $evaluated, $value);
        }

        return $value;
    }
}
