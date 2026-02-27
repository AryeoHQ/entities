<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Support\Entities\Contracts\Entity;
use Tooling\PhpStan\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Class_>
 */
#[NodeType(Class_::class)]
final class ModelMustHaveUsePolicy extends Rule
{
    /**
     * @param  Class_  $node
     */
    public function shouldHandle(Node $node, Scope $scope): bool
    {
        return $this->inherits($node, Model::class)
            && $this->inherits($node, Entity::class)
            && $this->doesNotHaveAttribute($node, UsePolicy::class);
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node, Scope $scope): void
    {
        $this->error(
            message: 'Model must have a #[UsePolicy] attribute.',
            line: $node->getStartLine(),
            identifier: 'entities.usePolicy',
        );
    }
}
