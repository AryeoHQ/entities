<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
final class ModelMustHaveHasFactory extends Rule
{
    /**
     * @param  Class_  $node
     */
    public function shouldHandle(Node $node, Scope $scope): bool
    {
        return $this->inherits($node, Model::class)
            && $this->inherits($node, Entity::class)
            && $this->doesNotInherit($node, HasFactory::class);
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node, Scope $scope): void
    {
        $this->error(
            message: 'Model must use the HasFactory trait.',
            line: $node->getStartLine(),
            identifier: 'entities.hasFactory',
        );
    }
}
