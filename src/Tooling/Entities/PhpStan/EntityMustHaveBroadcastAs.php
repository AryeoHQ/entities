<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Support\Entities\Events\Attributes\BroadcastAs;
use Support\Entities\Events\Contracts\ForEntity;
use Tooling\PhpStan\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Class_>
 */
#[NodeType(Class_::class)]
final class EntityMustHaveBroadcastAs extends Rule
{
    /**
     * @param  Class_  $node
     */
    public function shouldHandle(Node $node, Scope $scope): bool
    {
        return $this->inherits($node, ForEntity::class)
            && $this->doesNotHaveAttribute($node, BroadcastAs::class);
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node, Scope $scope): void
    {
        $this->error(
            message: 'ForEntity must have a #[BroadcastAs] attribute.',
            line: $node->getStartLine(),
            identifier: 'entities.broadcastAs',
        );
    }
}
