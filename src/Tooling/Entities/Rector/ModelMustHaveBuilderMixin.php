<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Support\Entities\Contracts\Entity;
use Tooling\Entities\Concerns\DetectsEloquentBuilder;
use Tooling\Rector\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Class_>
 */
#[NodeType(Class_::class)]
final class ModelMustHaveBuilderMixin extends Rule
{
    use DetectsEloquentBuilder;

    /**
     * @param  Class_  $node
     */
    public function shouldHandle(Node $node): bool
    {
        if (! $node instanceof Class_) {
            return false;
        }

        $builderClass = $this->getUseEloquentBuilderClass($node);

        return $this->inherits($node, Model::class)
            && $this->inherits($node, Entity::class)
            && $builderClass !== null
            && ! $this->hasBuilderMixin($node, $builderClass);
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node): Node
    {
        $builderClass = $this->getUseEloquentBuilderClass($node);

        if ($builderClass === null) {
            return $node;
        }

        $this->addMixinToDocblock($node, $this->getShortClassName($builderClass));

        return $node;
    }

    private function hasBuilderMixin(Class_ $node, string $builderClass): bool
    {
        $docComment = $node->getDocComment();

        if ($docComment === null) {
            return false;
        }

        $docText = $docComment->getText();
        $shortBuilderClass = $this->getShortClassName($builderClass);

        $shortPattern = '/@mixin\s+'.preg_quote($shortBuilderClass, '/').'(\s|$|\*)/';
        $fullPattern = '/@mixin\s+'.preg_quote($builderClass, '/').'(\s|$|\*)/';
        $fullyQualifiedPattern = '/@mixin\s+\\\\'.preg_quote($builderClass, '/').'(\s|$|\*)/';

        return preg_match($shortPattern, $docText) === 1
            || preg_match($fullPattern, $docText) === 1
            || preg_match($fullyQualifiedPattern, $docText) === 1;
    }

    private function addMixinToDocblock(Class_ $node, string $builderClass): void
    {
        $docComment = $node->getDocComment();
        $mixinLine = '@mixin '.$builderClass;

        if ($docComment === null) {
            $node->setDocComment(new Doc("/**\n * {$mixinLine}\n */"));

            return;
        }

        $docText = $docComment->getText();

        $newDocText = preg_replace(
            '/(\s*)\*\/\s*$/',
            " * {$mixinLine}\n$1*/",
            $docText
        );

        $node->setDocComment(new Doc($newDocText ?? $docText));
    }
}
