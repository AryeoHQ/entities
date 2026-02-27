<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use Support\Entities\Contracts\Entity;
use Tooling\Rector\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Class_>
 */
#[NodeType(Class_::class)]
final class ModelMustHaveCollectedBy extends Rule
{
    /**
     * @param  Class_  $node
     */
    public function shouldHandle(Node $node): bool
    {
        return $this->inherits($node, Model::class)
            && $this->inherits($node, Entity::class)
            && $this->doesNotHaveAttribute($node, CollectedBy::class);
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node): null|Node
    {
        $className = $node->name?->toString();

        if ($className === null) {
            return null;
        }

        $collectionFqcn = $this->deriveCollectionFqcn($node, $className);

        $attribute = new Attribute(
            new FullyQualified(CollectedBy::class),
            [new Arg(new ClassConstFetch(new FullyQualified($collectionFqcn), 'class'))],
        );

        array_unshift($node->attrGroups, new AttributeGroup([$attribute]));

        return $node;
    }

    private function deriveCollectionFqcn(Class_ $node, string $className): string
    {
        $namespace = $node->namespacedName?->slice(0, -1)?->toString() ?? '';
        $plural = Str::plural($className);

        return $namespace.'\\Collection\\'.$plural;
    }
}
