<?php

declare(strict_types=1);

namespace Presta\BehatEvaluator\Foundry;

use Doctrine\Inflector\Inflector;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

final class FactoryClassFactory
{
    private readonly string $namespace;

    public function __construct(string $namespace, private readonly Inflector $inflector)
    {
        if (!\str_ends_with($namespace, '\\')) {
            $namespace .= '\\';
        }

        $this->namespace = $namespace;
    }

    public function fromName(string $name): string
    {
        $name = implode('\\', array_map($this->inflector->classify(...), explode('/', $name)));

        $factoryClass = "$this->namespace{$name}Factory";
        if (!\is_a($factoryClass, PersistentProxyObjectFactory::class, true)) {
            throw new \InvalidArgumentException('You must define a valid factory class.');
        }

        return $factoryClass;
    }
}
