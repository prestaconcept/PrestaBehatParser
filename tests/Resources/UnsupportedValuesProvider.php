<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Resources;

trait UnsupportedValuesProvider
{
    /**
     * @param list<'array'|'bool'|'float'|'int'|'null'|'object'|'string'> $excludedTypes
     *
     * @return iterable<string, array{scalar, scalar}>
     */
    protected static function unsupportedValues(array $excludedTypes = []): iterable
    {
        yield from self::unsupportedScalarValues($excludedTypes);
        yield from self::unsupportedNonScalarValues($excludedTypes);
    }

    /**
     * @param list<'bool'|'float'|'int'|'string'> $excludedTypes
     *
     * @return iterable<string, array{scalar, scalar}>
     */
    protected static function unsupportedScalarValues(array $excludedTypes = []): iterable
    {
        foreach (Faker::scalars($excludedTypes) as $value) {
            yield self::createUnsupportedValueScenarioName($value) => [$value, $value];
        }
    }

    /**
     * @param list<'array'|'null'|'object'> $excludedTypes
     *
     * @return iterable<string, array{scalar, scalar}>
     */
    protected static function unsupportedNonScalarValues(array $excludedTypes = []): iterable
    {
        foreach (Faker::nonScalars($excludedTypes) as $value) {
            yield self::createUnsupportedValueScenarioName($value) => [$value, $value];
        }
    }

    private static function createUnsupportedValueScenarioName(mixed $value): string
    {
        $debugType = match (true) {
            \is_object($value) => 'object',
            default => \get_debug_type($value),
        };

        $prefix = match ($debugType) {
            'array', 'object' => 'an',
            'null' => 'a',
            default => 'a random',
        };

        $suffix = match ($debugType) {
            'null' => 'value',
            default => '',
        };

        return \trim("$prefix $debugType $suffix") . ' should return the original value unchanged';
    }
}
