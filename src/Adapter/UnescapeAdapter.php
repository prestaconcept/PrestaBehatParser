<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Adapter;

final class UnescapeAdapter implements AdapterInterface
{
    public function __invoke(mixed $value): mixed
    {
        if (!\is_string($value)) {
            return $value;
        }

        return \stripslashes($value);
    }
}
