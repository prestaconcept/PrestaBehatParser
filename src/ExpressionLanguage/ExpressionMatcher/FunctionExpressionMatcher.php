<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\ExpressionLanguage\ExpressionMatcher;

final class FunctionExpressionMatcher
{
    /**
     * @return list<string>
     */
    public function __invoke(string $name, string $text): array
    {
        \preg_match_all("/<(?<expression>$name\(.*\))>/U", $text, $matches);

        return $matches['expression'];
    }
}
