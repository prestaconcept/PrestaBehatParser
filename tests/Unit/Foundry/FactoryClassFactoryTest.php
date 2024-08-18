<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Unit\Foundry;

use Doctrine\Inflector\InflectorFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Presta\BehatEvaluator\Foundry\FactoryClassFactory;

final class FactoryClassFactoryTest extends TestCase
{
    #[DataProvider('names')]
    public function testCreatingAFactoryClass(\Throwable|string $expected, string $name, string $namespace): void
    {
        if ($expected instanceof \Throwable) {
            $this->expectException(\get_class($expected));

            if ('' !== $expected->getMessage()) {
                $this->expectExceptionMessage($expected->getMessage());
            }
        }

        $factory = new FactoryClassFactory($namespace, InflectorFactory::create()->build());

        $factoryClass = $factory->fromName($name);

        if ($expected instanceof \Throwable) {
            return;
        }

        self::assertSame($expected, $factoryClass);
    }

    /**
     * @return iterable<string, array{\Throwable|string, string, string}>
     */
    public static function names(): iterable
    {
        $namespace = 'Presta\\BehatEvaluator\\Tests\\Resources\\FactoryClassFactory';

        yield 'a simple name should create a simple namespace' => [
            "$namespace\\FooFactory",
            'foo',
            $namespace,
        ];
        yield 'a compound name should create a compound namespace' => [
            "$namespace\\FooBarFactory",
            'foo bar',
            $namespace,
        ];
        yield 'a slash separated name should create a backslash separated namespace' => [
            "$namespace\\Foo\\BarFactory",
            'foo/bar',
            $namespace,
        ];
        yield 'a simple name and a namespace ending with "\\" should not throw an exception' => [
            "$namespace\\FooFactory",
            'foo',
            "$namespace\\",
        ];
        yield 'a name matching no model factory should throw an exception' => [
            new \InvalidArgumentException('You must define a valid factory class.'),
            'invalid',
            $namespace,
        ];
    }
}
