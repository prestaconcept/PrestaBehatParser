<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Resources\FactoryClassFactory\Foo;

use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Bar>
 */
final class BarFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Bar::class;
    }

    protected function defaults(): array|callable
    {
        return [];
    }
}
