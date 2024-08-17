<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit\Adapter;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\Adapter\ConstantAdapter;
use Presta\BehatEvaluator\Tests\Resources\ConstantHolder;
use Presta\BehatEvaluator\Tests\Resources\ExpressionLanguageFactory;
use Presta\BehatEvaluator\Tests\Resources\UnsupportedValuesProvider;
use Symfony\Component\ExpressionLanguage\SyntaxError;

final class ConstantAdapterTest extends TestCase
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

        $evaluate = new ConstantAdapter(ExpressionLanguageFactory::create());
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
        yield 'a string containing only a PHP constant should return the constant\'s value' => [
            ARRAY_FILTER_USE_BOTH,
            '<constant("ARRAY_FILTER_USE_BOTH")>',
        ];
        yield 'a string containing only a non existing PHP constant'
            . ' should return the original expression unchanged' => [
            '<constant("UNDEFINED")>',
            '<constant("UNDEFINED")>',
        ];
        yield 'a string containing only a class constant should return the constant\'s value' => [
            ConstantHolder::STRING,
            '<constant("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\ConstantHolder::STRING")>',
        ];
        yield 'a string containing only a class constant from a non existing class'
            . ' should return the original expression unchanged' => [
            '<constant("Invalid\\\\Path::UNDEFINED")>',
            '<constant("Invalid\\\\Path::UNDEFINED")>',
        ];
        yield 'a string containing only a non existing class constant'
            . ' should return the original expression unchanged' => [
            '<constant("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\ConstantHolder::UNDEFINED")>',
            '<constant("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\ConstantHolder::UNDEFINED")>',
        ];
        yield 'a string containing only a valid constant but without the constant function'
            . ' should return the original expression unchanged' => [
            'Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\ConstantHolder::STRING',
            'Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\ConstantHolder::STRING',
        ];
        yield 'a string containing a constant expression'
            . ' should return the string after evaluating the constant expression' => [
            'the value ' . ARRAY_FILTER_USE_KEY . ' comes from a constant',
            'the value <constant("ARRAY_FILTER_USE_KEY")> comes from a constant',
        ];
        yield 'a string containing many constant expressions'
            . ' should return the string after evaluating the constant expressions' => [
            'the values ' . ARRAY_FILTER_USE_KEY . ' and ' . ARRAY_FILTER_USE_BOTH . ' come from constants',
            'the values <constant("ARRAY_FILTER_USE_KEY")> and <constant("ARRAY_FILTER_USE_BOTH")> come from constants',
        ];
        yield from self::unsupportedValues(['string']);
        yield 'a string containing a malformed argument should throw a syntax error' => [
            new SyntaxError('Variable "this" is not valid', 10, 'constant(this is a malformed argument)'),
            '<constant(this is a malformed argument)>',
        ];
        yield 'a string containing an expression returning a non scalar value, should throw a runtime exception' => [
            new \RuntimeException(),
            'the value <constant("Presta\\\\BehatEvaluator\\\\Tests\\\\Resources\\\\ConstantHolder::ARRAY")>'
            . ' is not scalar',
        ];
    }
}
