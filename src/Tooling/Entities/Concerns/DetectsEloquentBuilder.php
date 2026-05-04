<?php

declare(strict_types=1);

namespace Tooling\Entities\Concerns;

use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;

trait DetectsEloquentBuilder
{
    private function getUseEloquentBuilderClass(Class_ $node): null|string
    {
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $attrName = $attr->name->toString();

                if ($attrName !== 'UseEloquentBuilder' && $attrName !== UseEloquentBuilder::class) {
                    continue;
                }

                $arg = $attr->args[0]->value ?? null;

                if ($arg instanceof Node\Expr\ClassConstFetch && $arg->class instanceof Node\Name) {
                    return $arg->class->toString();
                }
            }
        }

        return null;
    }

    private function getShortClassName(string $className): string
    {
        $parts = explode('\\', $className);

        return end($parts);
    }
}
