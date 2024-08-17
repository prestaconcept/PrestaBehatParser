<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\Adapter\EnumAdapter;
use Presta\BehatEvaluator\Tests\Resources\ExpressionLanguageFactory;
use Presta\BehatEvaluator\Tests\Resources\FooBarEnum;
use Presta\BehatEvaluator\Tests\Resources\PriorityEnum;
use Presta\BehatEvaluator\Tests\Resources\StatusEnum;
use Presta\BehatEvaluator\Tests\Resources\UnsupportedValuesProvider;
use Symfony\Component\ExpressionLanguage\SyntaxError;

final class EnumAdapterTest extends TestCase
{
    use UnsupportedValuesProvider;

    #[DataProvider('values')]
    public function testInvokingTheAdapter(mixed $expected, mixed $value): void
    {
        if ($expected instanceof \Throwable) {
            $this->expectException(get_class($expected));

            if ('' !== $expected->getMessage()) {
                $this->expectExceptionMessage($expected->getMessage());
            }
        }

        $evaluate = new EnumAdapter(ExpressionLanguageFactory::create());
        $value = $evaluate($value);

        if ($expected instanceof \Throwable) {
            return;
        }

        self::assertSame($expected, $value);
    }

    /**
     * @return iterable<string, array{mixed, mixed}>
     */
    public static function values(): iterable
    {
        yield 'a string containing only a unit enum should return the enum' => [
            FooBarEnum::Foo,
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\FooBarEnum::Foo")>',
        ];
        yield 'a string containing only an int backed enum should return the enum' => [
            PriorityEnum::High,
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\PriorityEnum::High")>',
        ];
        yield 'a string containing only a string backed enum should return the enum' => [
            StatusEnum::Todo,
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\StatusEnum::Todo")>',
        ];
        yield 'a string containing only a unit enum and requesting it\'s value should throw a runtime exception' => [
            new \RuntimeException('You can not get the "value" of a UnitEnum.'),
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\FooBarEnum::Foo", "value")>',
        ];
        yield 'a string containing only an int backed enum and requesting it\'s value'
            . ' should return the enum\'s value' => [
            PriorityEnum::High->value,
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\PriorityEnum::High", "value")>',
        ];
        yield 'a string containing only a string backed enum and requesting it\'s value'
            . ' should return the enum\'s value' => [
            StatusEnum::Todo->value,
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\StatusEnum::Todo", "value")>',
        ];
        yield 'a string containing only a unit enum and requesting it\'s name'
            . ' should throw a runtime exception' => [
            new \RuntimeException('You can not get the "name" of a UnitEnum.'),
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\FooBarEnum::Foo", "name")>',
        ];
        yield 'a string containing only an int backed enum and requesting it\'s name'
            . ' should return the enum\'s name' => [
            PriorityEnum::High->name,
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\PriorityEnum::High", "name")>',
        ];
        yield 'a string containing only a string backed enum and requesting it\'s name'
            . ' should return the enum\'s name' => [
            StatusEnum::Todo->name,
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\StatusEnum::Todo", "name")>',
        ];
        yield 'a string containing only an enum class should return the original expression unchanged' => [
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\FooBarEnum")>',
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\FooBarEnum")>',
        ];
        yield 'a string containing only a constant should return the original expression unchanged' => [
            '<enum("ARRAY_FILTER_USE_BOTH")>',
            '<enum("ARRAY_FILTER_USE_BOTH")>',
        ];
        yield 'a string containing only a non enum string should return the original expression unchanged' => [
            '<enum("Undefined")>',
            '<enum("Undefined")>',
        ];
        yield 'a string containing only a non existing enum class should return the original expression unchanged' => [
            '<enum("Invalid\\\\Path::Undefined")>',
            '<enum("Invalid\\\\Path::Undefined")>',
        ];
        yield 'a string containing only a non existing enum should return the original expression unchanged' => [
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\FooBarEnum::Undefined")>',
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\FooBarEnum::Undefined")>',
        ];
        yield 'a string containing only a valid enum but without the enum function'
            . ' should return the original expression unchanged' => [
            'Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\FooBarEnum::Foo',
            'Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\FooBarEnum::Foo',
        ];
        yield 'a string containing a unit enum expression should throw a runtime exception' => [
            new \RuntimeException('You can not get the "value" of a UnitEnum.'),
            'the value <enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\FooBarEnum::Foo")>'
                . ' comes from an enum',
        ];
        yield 'a string containing an int backed enum expression'
            . ' should return the string after evaluating the enum expression' => [
            'the value ' . PriorityEnum::Default->value . ' comes from an enum',
            'the value <enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\PriorityEnum::Default")>'
                . ' comes from an enum',
        ];
        yield 'a string containing a string backed enum expression'
            . ' should return the string after evaluating the enum expression' => [
            'the value ' . StatusEnum::Doing->value . ' comes from an enum',
            'the value <enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\StatusEnum::Doing")>'
                . ' comes from an enum',
        ];
        yield 'a string containing a unit enum expression and requesting it\'s value'
            . ' should throw a runtime exception' => [
            new \RuntimeException('You can not get the "value" of a UnitEnum.'),
            'the value <enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\FooBarEnum::Foo", "value")>'
                . ' comes from an enum',
        ];
        yield 'a string containing an int backed enum expression and requesting it\'s value'
            . ' should return the string after evaluating the enum expression' => [
            'the value ' . PriorityEnum::Default->value . ' comes from an enum',
            'the value <enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\PriorityEnum::Default", "value")>'
                . ' comes from an enum',
        ];
        yield 'a string containing a string backed enum expression and requesting it\'s value'
            . ' should return the string after evaluating the enum expression' => [
            'the value ' . StatusEnum::Doing->value . ' comes from an enum',
            'the value <enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\StatusEnum::Doing", "value")>'
                . ' comes from an enum',
        ];
        yield 'a string containing many enum expressions'
            . ' should return the string after evaluating the enum expressions' => [
            'the values ' . PriorityEnum::Low->value . ' and ' . StatusEnum::Done->value . ' come from enums',
            'the values <enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\PriorityEnum::Low")>'
                . ' and <enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\StatusEnum::Done")>'
                . ' come from enums',
        ];
        yield from self::unsupportedValues(['string']);
        yield 'a string containing only a string backed enum and requesting a non existing property'
            . ' should throw a runtime exception' => [
            new \RuntimeException('You can not get the "invalid" property of an enum.'),
            '<enum("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\StatusEnum::Todo", "invalid")>',
        ];
        yield 'a string containing a malformed argument should throw a syntax error' => [
            new SyntaxError('Variable "this" is not valid', 6, 'enum(this is a malformed argument)'),
            '<enum(this is a malformed argument)>',
        ];
    }
}
