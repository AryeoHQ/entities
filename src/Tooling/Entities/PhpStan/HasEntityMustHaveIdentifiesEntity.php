<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Entity\IdentifiesEntity;
use Tooling\PhpStan\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Class_>
 */
#[NodeType(Class_::class)]
final class HasEntityMustHaveIdentifiesEntity extends Rule
{
    /**
     * @param  Class_  $node
     */
    public function shouldHandle(Node $node, Scope $scope): bool
    {
        return $this->inherits($node, ForEntity::class)
            && ! $this->hasEntityProperty($node);
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node, Scope $scope): void
    {
        $this->error(
            message: sprintf(
                '%s must have a property with the #[%s] attribute.',
                class_basename(ForEntity::class),
                class_basename(IdentifiesEntity::class)
            ),
            line: $node->name?->getStartLine() ?? $node->getStartLine(),
            identifier: 'entities.HasEntity.IdentifiesEntity.required',
        );
    }

    private function hasEntityProperty(Class_ $node): bool
    {
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof Property && $this->hasAttribute($stmt, IdentifiesEntity::class)) {
                return true;
            }

            if ($stmt instanceof ClassMethod && $stmt->name->toString() === '__construct') {
                foreach ($stmt->params as $param) {
                    if ($param->flags !== 0 && $this->hasAttribute($param, IdentifiesEntity::class)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
