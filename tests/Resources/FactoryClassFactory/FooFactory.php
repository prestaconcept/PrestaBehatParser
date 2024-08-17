<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Resources\FactoryClassFactory;

use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Foo>
 */
final class FooFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Foo::class;
    }

    protected function defaults(): array|callable
    {
        return [];
    }
}
