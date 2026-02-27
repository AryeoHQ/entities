<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\Factories\Factory;
use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use Tooling\PhpStan\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Node\Expr>
 */
#[NodeType(Node\Expr::class)]
final class FactoryMustNotBeUsedDirectly extends Rule
{
    /**
     * @param  Node\Expr  $node
     */
    public function shouldHandle(Node $node, Scope $scope): bool
    {
        if (! ($node instanceof New_ || $node instanceof StaticCall || $node instanceof ClassConstFetch)) {
            return false;
        }

        if ($node instanceof ClassConstFetch && $node->name instanceof Identifier && $node->name->name === 'class') {
            return false;
        }

        $class = $this->findClassName($node, $scope);

        if ($class === null) {
            return false;
        }

        $classReflection = (new ObjectType($class))->getClassReflection();

        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return $this->inherits($classReflection, Factory::class);
    }

    /**
     * @param  Node\Expr  $node
     */
    public function handle(Node $node, Scope $scope): void
    {
        $this->error(
            message: 'Direct use of Factory is disallowed; use `Model::factory()` instead.',
            line: $node->getStartLine(),
            identifier: 'entities.factoryDirectUsage',
        );
    }

    private function findClassName(Node\Expr $node, Scope $scope): null|string
    {
        if (($node instanceof New_ || $node instanceof StaticCall || $node instanceof ClassConstFetch)
            && $node->class instanceof Node\Name) {
            return $scope->resolveName($node->class);
        }

        return null;
    }
}
