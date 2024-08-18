<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Integration\Adapter;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use Presta\BehatEvaluator\Adapter\FactoryAdapter;
use Presta\BehatEvaluator\Exception\UnexpectedTypeException;
use Presta\BehatEvaluator\Tests\Application\Entity\User;
use Presta\BehatEvaluator\Tests\Application\Foundry\Factory\UserFactory;
use Presta\BehatEvaluator\Tests\Integration\KernelTestCase;
use Presta\BehatEvaluator\Tests\Resources\ExpressionLanguageFactory;
use Presta\BehatEvaluator\Tests\Resources\UnsupportedValuesProvider;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Test\Factories;

final class FactoryAdapterTest extends KernelTestCase
{
    use Factories;
    use UnsupportedValuesProvider;

    #[DataProvider('values')]
    public function testInvokingTheAdapter(mixed $expected, mixed $value): void
    {
        if ($expected instanceof \Throwable) {
            $this->expectException(\get_class($expected));

            if ('' !== $expected->getMessage()) {
                $this->expectExceptionMessage($expected->getMessage());
            }
        }

        UserFactory::createOne(['firstname' => 'John', 'lastname' => 'Doe']);
        UserFactory::createOne(['firstname' => 'Jane', 'lastname' => 'Doe']);

        $evaluate = new FactoryAdapter(ExpressionLanguageFactory::create());
        $value = $evaluate($value);

        if ($expected instanceof \Throwable) {
            return;
        }

        if (\is_callable($expected)) {
            $expected = $expected();
        }

        // ModelFactory::randomSet() does not return sorted entities
        if ($value instanceof ArrayCollection) {
            $data = $value->toArray();
            \usort($data, static fn (User $left, User $right): int => $left->getId() <=> $right->getId());

            $value = new ArrayCollection($data);
        }

        self::assertEquals($expected, $value);
    }

    /**
     * @return iterable<string, array{mixed, mixed}>
     */
    public static function values(): iterable
    {
        yield 'a string containing only a factory expression with a factory name and an array of attributes'
        . ' should find and return the relevant object proxy' => [
            static fn () => UserFactory::find(['firstname' => 'John'])->_disableAutoRefresh(),
            '<factory("user", {"firstname": "John"})>',
        ];
        yield 'a string containing only a factory expression'
        . ' with a factory name, an array of attributes and an accessor'
        . ' should find and return the relevant object proxy\'s accessible property' => [
            'John',
            '<factory("user", {"firstname": "John"}, "firstname")>',
        ];
        yield 'a string containing only a factory expression'
        . ' with a factory name, a search method and an array of attributes'
        . ' should find and return the relevant object proxy(s)' => [
            static fn () => new ArrayCollection(
                \array_map(
                    static fn (Proxy $proxy): object => $proxy->_real(),
                    UserFactory::findBy(['lastname' => 'Doe']),
                ),
            ),
            '<factory("user", "findBy", {"lastname": "Doe"})>',
        ];
        yield 'a string containing only a factory expression with a factory name and a non search method'
        . ' should return the relevant result' => [
            2,
            '<factory("user", "count")>',
        ];
        yield 'a string containing a factory expression with a factory name, the "randomSet" function'
        . ' and an integer as third parameter should return the relevant object proxy(s)' => [
            static fn () => new ArrayCollection(
                \array_map(
                    static fn (Proxy $proxy): object => $proxy->_real(),
                    UserFactory::all(),
                ),
            ),
            '<factory("user", "randomSet", 2)>',
        ];
        yield 'a string containing a factory expression with a factory name, the "randomSet" function,'
        . ' an integer as third parameter and an array of attributes'
        . ' should return the relevant object proxy(s)' => [
            static fn () => new ArrayCollection(
                \array_map(
                    static fn (Proxy $proxy): object => $proxy->_real(),
                    UserFactory::findBy(['firstname' => 'John']),
                ),
            ),
            '<factory("user", "randomSet", 1, {"firstname": "John"})>',
        ];
        yield 'a string containing a factory expression'
        . ' should return the string after evaluating the factory expression' => [
            'The value John comes from a string',
            'The value <factory("user", {"firstname": "John"}, "firstname")> comes from a string',
        ];
        yield 'a string containing many factory expressions'
        . ' should return the string after evaluating the factory expressions' => [
            'The values John and 2 come from a string',
            'The values <factory("user", {"firstname": "John"}, "firstname")>'
            . ' and <factory("user", {"firstname": "Jane"}, "id")> come from a string',
        ];
        yield from self::unsupportedValues(['string']);
        yield 'a string containing a factory expression without arguments should throw an exception' => [
            new \ArgumentCountError(),
            '<factory()>',
        ];
        yield 'a string containing a factory expression with an invalid factory class should throw an exception' => [
            new \InvalidArgumentException(),
            '<factory("foo", "count")>',
        ];
        yield 'a string containing a factory expression with an invalid method should throw an exception' => [
            new \BadMethodCallException(),
            '<factory("user", "foo")>',
        ];
        yield 'a string containing a factory expression with a search method but no attributes'
        . ' should throw an exception' => [
            new \LogicException(),
            '<factory("user", "find")>',
        ];
        yield 'a string containing a factory expression with a method, attributes and an accessor'
        . ' should throw an exception' => [
            new UnexpectedTypeException([], Proxy::class),
            '<factory("user", "findBy", {"lastname": "Doe"}, "firstname")>',
        ];
    }
}
