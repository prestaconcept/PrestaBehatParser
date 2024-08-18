<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Adapter;

final class ScalarAdapter implements AdapterInterface
{
    public function __invoke(mixed $value): mixed
    {
        if (!\is_scalar($value)) {
            return $value;
        }

        return match (true) {
            'null' === $value => null,
            'true' === $value => true,
            'false' === $value => false,
            (int)$value == $value => (int)$value,
            (float)$value == $value => (float)$value,
            default => $value,
        };
    }
}
