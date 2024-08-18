<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Resources;

final class Faker
{
    /**
     * @param list<'bool'|'float'|'int'|'string'> $excludedTypes
     *
     * @return list<scalar>
     */
    public static function scalars(array $excludedTypes = []): array
    {
        $booleans = [true, false];
        $floats = \range(-5.5, 5.5);
        $ints = \range(-5, 5);
        $strings = ['foo', 'bar', 'Lorem Ipsum'];

        /** @var list<scalar> $values */
        $values = [
            $booleans[\array_rand($booleans)],
            $floats[\array_rand($floats)],
            $ints[\array_rand($ints)],
            $strings[\array_rand($strings)],
        ];

        return \array_filter(
            $values,
            static fn (bool|float|int|string $value): bool => !\in_array(\get_debug_type($value), $excludedTypes, true),
        );
    }

    /**
     * @param list<'array'|'null'|'object'> $excludedTypes
     *
     * @return list<array<mixed>|null|object>
     */
    public static function nonScalars(array $excludedTypes = []): array
    {
        /** @var list<array<mixed>|null|object> $values */
        $values = [
            null,
            [],
            new \stdClass(),
        ];

        return \array_filter(
            $values,
            static function (array|null|object $value) use ($excludedTypes): bool {
                if (\is_object($value) && \in_array('object', $excludedTypes, true)) {
                    return false;
                }

                return !\in_array(\get_debug_type($value), $excludedTypes, true);
            },
        );
    }
}
