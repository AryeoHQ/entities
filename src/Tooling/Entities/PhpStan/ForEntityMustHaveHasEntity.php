<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Provides\HasEntity;
use Tooling\PhpStan\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Class_>
 */
#[NodeType(Class_::class)]
final class ForEntityMustHaveHasEntity extends Rule
{
    /**
     * @param  Class_  $node
     */
    public function shouldHandle(Node $node, Scope $scope): bool
    {
        return $this->inherits($node, ForEntity::class)
            && $this->doesNotInherit($node, HasEntity::class);
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node, Scope $scope): void
    {
        $this->error(
            message: sprintf(
                '%s must use %s.',
                class_basename(ForEntity::class),
                class_basename(HasEntity::class)
            ),
            line: $node->name?->getStartLine() ?? $node->getStartLine(),
            identifier: 'entities.ForEntity.HasEntity.required',
        );
    }
}
