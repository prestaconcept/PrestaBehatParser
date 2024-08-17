<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\Adapter\JsonAdapter;
use Presta\BehatEvaluator\Tests\Resources\UnsupportedValuesProvider;

final class JsonAdapterTest extends TestCase
{
    use UnsupportedValuesProvider;

    #[DataProvider('values')]
    public function testInvokingTheAdapter(mixed $expected, mixed $value): void
    {
        $evaluate = new JsonAdapter();

        self::assertSame($expected, $evaluate($value));
    }

    /**
     * @return iterable<string, array{mixed, mixed}>
     */
    public static function values(): iterable
    {
        yield 'a string should return the same string' => ['foo', 'foo'];
        yield 'an empty json array should return an empty php array' => [[], '[]'];
        yield 'an empty json object should return an empty php array' => [[], '{}'];
        yield 'a basic json array should return the corresponding php array' => [['foo', 'bar'], '["foo", "bar"]'];
        yield 'a basic json object should return the corresponding php array' => [['foo' => 'bar'], '{"foo": "bar"}'];
        yield from self::unsupportedValues(['string']);
    }
}
