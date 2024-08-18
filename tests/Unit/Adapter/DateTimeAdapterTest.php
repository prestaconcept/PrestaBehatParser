<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\Adapter\DateTimeAdapter;
use Presta\BehatEvaluator\Tests\Resources\ExpressionLanguageFactory;
use Presta\BehatEvaluator\Tests\Resources\UnsupportedValuesProvider;

final class DateTimeAdapterTest extends TestCase
{
    use UnsupportedValuesProvider;

    #[DataProvider('values')]
    public function testInvokingTheAdapter(mixed $expected, mixed $value): void
    {
        $parse = new DateTimeAdapter(ExpressionLanguageFactory::create());
        $value = $parse($value);

        if ($expected instanceof \DateTimeInterface) {
            self::assertInstanceOf(\get_class($expected), $value);
        }

        // create a new object to ignore the seconds and milliseconds to prevent comparison issues
        if ($expected instanceof \DateTimeInterface) {
            $expected = \DateTime::createFromInterface($expected)->setTime(
                (int)$expected->format('H'),
                (int)$expected->format('i'),
            );
        }

        // create a new object to ignore the seconds and milliseconds to prevent comparison issues
        if ($value instanceof \DateTimeInterface) {
            $value = \DateTime::createFromInterface($value)->setTime(
                (int)$value->format('H'),
                (int)$value->format('i'),
            );
        }

        self::assertEquals($expected, $value);
    }

    /**
     * @return iterable<string, array{mixed, mixed}>
     */
    public static function values(): iterable
    {
        foreach (['datetime' => '\DateTime', 'datetime_immutable' => '\DateTimeImmutable'] as $name => $className) {
            yield "a string containing only a $name expression with no parameters"
            . " should return a $className object set to the current time" => [
                new $className(),
                "<$name()>",
            ];
            yield "a string containing only a $name expression with a \"time\" parameter"
            . " should return a $className object set to the given time" => [
                new $className('2023-01-01 00:00:00'),
                "<$name(\"2023-01-01 00:00:00\")>",
            ];
            yield "a string containing only a $name expression with a \"time\" parameter and a \"format\" parameter"
            . ' should return the given datetime string formatted with the given format' => [
                '01/01/2023',
                "<$name(\"2023-01-01\", \"d/m/Y\")>",
            ];
            yield "a string containing only a $name expression with a \"time\" parameter"
            . ' and a named parameter "format"'
            . ' should return the given datetime string formatted with the given format' => [
                '01/01/2023',
                "<$name(\"2023-01-01\", format: \"d/m/Y\")>",
            ];
            yield "a string containing only a $name expression with no \"time\" parameter"
            . ' but a named parameter "format"'
            . ' should return the current datetime string formatted with the given format' => [
                \date('d/m/Y'),
                "<$name(format: \"d/m/Y\")>",
            ];
            yield "a string containing only a $name expression with no \"time\" parameter"
            . ' but a named parameter "intl"'
            . ' should return the current datetime string formatted with the given intl format' => [
                \date('d/m/Y'),
                "<$name(intl: {\"date\": \"SHORT\"})>",
            ];
            yield "a string containing only a $name expression with a \"time\" parameter"
            . ' and an "intl" parameter specifying only the date format'
            . ' should return the given datetime string formatted with the given intl date format' => [
                '1 janv. 2023',
                "<$name(\"2023-01-01\", {\"date\": \"MEDIUM\"})>",
            ];
            yield "a string containing only a $name expression with a \"time\" parameter"
            . ' and a named parameter "intl" specifying only the time format'
            . ' should return the given datetime string formatted with the given intl time format' => [
                '08:00',
                "<$name(\"2023-01-01 08:00:00\", intl: {\"time\": \"SHORT\"})>",
            ];
            yield "a string containing only a $name expression with a \"time\" parameter"
            . ' and an "intl" parameter specifying a date and a time format'
            . ' should return the given datetime string formatted with the given intl date and time formats' => [
                '1 janvier 2023 à 08:00:00',
                "<$name(\"2023-01-01 08:00:00\", {\"date\": \"LONG\", \"time\": \"MEDIUM\"})>",
            ];
            yield "a string containing a $name expression escaped with single quotes"
            . " should return the string after evaluating the $name expression" => [
                'the value 1 janv. 2023 is escaped with single quotes',
                "the value <$name('2023-01-01 00:00:00', {'date': 'MEDIUM'})> is escaped with single quotes",
            ];
            yield "a string containing a $name expression"
            . " should return the string after evaluating the $name expression" => [
                'the value 01/01/2023 comes from a string',
                "the value <$name(\"2023-01-01\", \"d/m/Y\")> comes from a string",
            ];
        }

        yield 'a string containing many datetime and datetime_immutable expressions'
        . ' should return the string after evaluating the datetime and datetime_immutable expressions' => [
            'the values 01/01/2023 and 08:00 come from a string',
            'the values <datetime("2023-01-01", "d/m/Y")> and <datetime_immutable("08:00", "H:i")> come from a string',
        ];
        yield 'a string should return the same string' => ['this is a string', 'this is a string'];
        yield from self::unsupportedValues(['string']);
    }
}
