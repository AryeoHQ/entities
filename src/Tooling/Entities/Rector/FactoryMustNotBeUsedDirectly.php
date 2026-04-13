<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use Illuminate\Database\Eloquent\Factories\Factory;
use PhpParser\Node;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use Tooling\Rector\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Node>
 */
#[NodeType(StaticCall::class)]
#[NodeType(New_::class)]
final class FactoryMustNotBeUsedDirectly extends Rule
{
    /**
     * @param  StaticCall|New_  $node
     */
    public function shouldHandle(Node $node): bool
    {
        if (! $node->class instanceof Node\Name) {
            return false;
        }

        $className = ltrim($node->class->toString(), '\\');

        if (! $this->isFactoryClass($className)) {
            return false;
        }

        if ($node instanceof StaticCall) {
            return $node->name instanceof Identifier && $node->name->name === 'new';
        }

        return true;
    }

    /**
     * @param  StaticCall|New_  $node
     */
    public function handle(Node $node): null|Node
    {
        $className = ltrim($node->class->toString(), '\\');
        $modelClass = $this->resolveModelClass($className);

        if ($modelClass === null) {
            return null;
        }

        return new StaticCall(
            new FullyQualified($modelClass),
            'factory',
            $node instanceof StaticCall ? $node->args : [],
        );
    }

    private function isFactoryClass(string $className): bool
    {
        if ($className === Factory::class) {
            return true;
        }

        if (! class_exists($className)) {
            return false;
        }

        return is_subclass_of($className, Factory::class);
    }

    private function resolveModelClass(string $factoryClass): null|string
    {
        if (! class_exists($factoryClass)) {
            return null;
        }

        try {
            $reflection = new \ReflectionClass($factoryClass);
            $modelProperty = $reflection->getProperty('model');
            $defaultValue = $modelProperty->getDefaultValue();

            if (is_string($defaultValue) && class_exists($defaultValue)) {
                return ltrim($defaultValue, '\\');
            }
        } catch (\ReflectionException) {
            // $model property not found or not accessible
        }

        return null;
    }
}
