<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Adapter;

final class NthAdapter implements AdapterInterface
{
    public function __invoke(mixed $value): mixed
    {
        if (!\is_string($value)) {
            return $value;
        }

        \preg_match('/^(?<count>\d+)(st|nd|rd|th)$/', $value, $matches);
        if ('' === ($matches['count'] ?? '')) {
            return $value;
        }

        return (int)$matches['count'];
    }
}
