<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\ExpressionLanguage\Evaluator;

final class EnumEvaluator
{
    /**
     * @param array<string, mixed> $arguments
     */
    public function __invoke(array $arguments, string $enumClass, string $property = null): mixed
    {
        try {
            $enum = \constant($enumClass);
        } catch (\Throwable) {
            throw new \RuntimeException("\"$enumClass\" is not a valid enum.");
        }

        if (!$enum instanceof \UnitEnum) {
            $debugType = \get_debug_type($enum);

            throw new \RuntimeException("\"$debugType\" is not a valid enum.");
        }

        if (null === $property) {
            return $enum;
        }

        if (!\in_array($property, ['name', 'value'], true)) {
            throw new \RuntimeException("You can not get the \"$property\" property of an enum.");
        }

        if (!$enum instanceof \BackedEnum) {
            throw new \RuntimeException("You can not get the \"$property\" of a UnitEnum.");
        }

        return match ($property) {
            'value' => $enum->value,
            'name' => $enum->name,
        };
    }
}
