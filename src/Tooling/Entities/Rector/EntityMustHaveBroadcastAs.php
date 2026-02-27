<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use Support\Entities\Events\Attributes\BroadcastAs;
use Support\Entities\Events\Contracts\ForEntity;
use Tooling\Rector\Rules\Rule;
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
    public function shouldHandle(Node $node): bool
    {
        return $this->inherits($node, ForEntity::class)
            && $this->doesNotHaveAttribute($node, BroadcastAs::class);
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

        $broadcastName = $this->deriveBroadcastName($className);

        $attribute = new Attribute(
            new FullyQualified(BroadcastAs::class),
            [new Arg(new String_($broadcastName))],
        );

        array_unshift($node->attrGroups, new AttributeGroup([$attribute]));

        return $node;
    }

    private function deriveBroadcastName(string $className): string
    {
        $parts = preg_split('/(?=[A-Z])/', $className, -1, PREG_SPLIT_NO_EMPTY);

        if (! is_array($parts)) {
            return '';
        }

        return strtolower(implode('.', $parts));
    }
}
