<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser;
use Support\Entities\Contracts\Entity;
use Tooling\PhpStan\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Class_>
 */
#[NodeType(Class_::class)]
final class ModelMustNotDefineScopeMethods extends Rule
{
    /** @var Collection<int, ClassMethod> */
    private Collection $scopedMethods;

    /**
     * @param  Class_  $node
     */
    public function prepare(Node $node, Analyser\Scope $scope): void
    {
        $this->scopedMethods = collect($node->stmts)
            ->filter(fn (Node\Stmt $stmt): bool => $stmt instanceof ClassMethod)
            ->filter(fn (ClassMethod $method): bool => $this->isScopePrefixed($method) || $this->hasScopeAttribute($method));
    }

    /**
     * @param  Class_  $node
     */
    public function shouldHandle(Node $node, Analyser\Scope $scope): bool
    {
        return $this->inherits($node, Model::class)
            && $this->inherits($node, Entity::class)
            && $this->scopedMethods->isNotEmpty();
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node, Analyser\Scope $scope): void
    {
        $this->scopedMethods->each(fn (ClassMethod $method) => $this->error(
            message: 'Scopes should be defined on the Builder.',
            line: $method->name->getStartLine(),
            identifier: 'entities.Model.ScopeMethod.notAllowed',
        ));
    }

    private function isScopePrefixed(ClassMethod $method): bool
    {
        return $method->isPublic()
            && str_starts_with($method->name->toString(), 'scope')
            && $this->firstParameterIsBuilder($method);
    }

    private function firstParameterIsBuilder(ClassMethod $method): bool
    {
        $firstParam = $method->getParams()[0] ?? null;

        if ($firstParam?->type === null) {
            return false;
        }

        return $this->isBuilderType($firstParam->type);
    }

    private function isBuilderType(Node $type): bool
    {
        if ($type instanceof Node\Name) {
            return is_a($type->toString(), Builder::class, true);
        }

        if ($type instanceof Node\UnionType || $type instanceof Node\IntersectionType) {
            foreach ($type->types as $inner) {
                if ($this->isBuilderType($inner)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function hasScopeAttribute(ClassMethod $method): bool
    {
        return $this->hasAttribute($method, Scope::class);
    }
}
