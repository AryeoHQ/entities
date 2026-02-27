<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Support\Entities\Contracts\Entity;
use Tooling\Rector\Rules\Rule;
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
    public function shouldHandle(Node $node): bool
    {
        return $this->inherits($node, Model::class)
            && $this->inherits($node, Entity::class)
            && $this->doesNotInherit($node, HasFactory::class);
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node): Node
    {
        $this->addTrait($node, HasFactory::class);

        return $node;
    }
}
