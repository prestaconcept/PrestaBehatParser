<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Adapter;

final class JsonAdapter implements AdapterInterface
{
    public function __invoke(mixed $value): mixed
    {
        if (!\is_string($value)) {
            return $value;
        }

        $data = \json_decode($value, true);
        if (\is_array($data)) {
            return $data;
        }

        return $value;
    }
}
