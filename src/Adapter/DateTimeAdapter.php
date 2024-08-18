<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Adapter;

use Presta\BehatEvaluator\ExpressionLanguage\ExpressionLanguage;
use Presta\BehatEvaluator\ExpressionLanguage\ExpressionMatcher\FunctionExpressionMatcher;

/**
 * @example <datetime()>
 * @example <datetime_immutable()>
 * @example <datetime("tomorrow")>
 * @example <datetime("2023-01-01")>
 * @example <datetime(format: "Y-m-d")>
 * @example <datetime("yesterday", "Y-m-d H:i:s")>
 * @example <datetime("yesterday", format: "Y-m-d H:i:s")>
 * @example <datetime({"date": "MEDIUM"})>
 * @example <datetime(intl: {"time": "MEDIUM"})>
 * @example <datetime("now", {"date": "LONG", "time": "SHORT"})>
 * @example <datetime("now", intl: {"date": "SHORT", "time": "LONG"})>
 */
final class DateTimeAdapter implements AdapterInterface
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

        $expressions = $match('datetime(_immutable)?', $value);
        if ([] === $expressions) {
            return $value;
        }

        foreach ($expressions as $expression) {
            // surround named parameters arguments by double quotes
            // so that the named parameter part is not interpreted by the expression language
            // ex. 'format: "Y-m-d"' will be transformed to '"format: \"Y-m-d\""'
            $quotedExpression = \preg_replace_callback(
                '/(format|intl): ?[^,)]+/',
                static function (array $matches): string {
                    $value = \addslashes($matches[0] ?? '');

                    return "\"$value\"";
                },
                $expression,
            );

            \assert(\is_string($quotedExpression));

            $evaluated = $this->expressionLanguage->evaluate($quotedExpression);
            if ($evaluated instanceof \DateTimeInterface) {
                return $evaluated;
            }

            \assert(\is_string($evaluated));

            $value = \str_replace("<$expression>", $evaluated, $value);
        }

        return $value;
    }
}
