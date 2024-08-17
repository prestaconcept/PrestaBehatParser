<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Tests\Resources\FactoryClassFactory;

use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<FooBar>
 */
final class FooBarFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return FooBar::class;
    }

    protected function defaults(): array|callable
    {
        return [];
    }
}
