<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Exception;

final class UnexpectedTypeException extends \RuntimeException
{
    public function __construct(mixed $value, string $expectedType)
    {
        $actualType = \get_debug_type($value);

        parent::__construct("Expected argument of type \"$expectedType\", \"$actualType\" given.");
    }
}
