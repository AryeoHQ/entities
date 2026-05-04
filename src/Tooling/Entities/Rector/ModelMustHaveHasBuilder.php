<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use Support\Entities\Contracts\Entity;
use Tooling\Entities\Concerns\DetectsEloquentBuilder;
use Tooling\Rector\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Class_>
 */
#[NodeType(Class_::class)]
final class ModelMustHaveHasBuilder extends Rule
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
            && ! $this->hasBuilderTraitWithAnnotation($node, $builderClass);
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

        $this->addTrait($node, HasBuilder::class);
        $this->addAnnotationToTraitUse($node, $this->getShortClassName($builderClass));

        return $node;
    }

    private function hasBuilderTraitWithAnnotation(Class_ $node, string $builderClass): bool
    {
        $traitUse = $this->getHasBuilderTraitUse($node);

        if ($traitUse === null) {
            return false;
        }

        $docComment = $traitUse->getDocComment();

        if ($docComment === null) {
            return false;
        }

        $docText = $docComment->getText();
        $shortBuilderClass = $this->getShortClassName($builderClass);

        $shortPattern = '/@use\s+HasBuilder<'.preg_quote($shortBuilderClass, '/').'>/';
        $fullPattern = '/@use\s+HasBuilder<'.preg_quote($builderClass, '/').'>/';
        $fullyQualifiedPattern = '/@use\s+HasBuilder<\\\\'.preg_quote($builderClass, '/').'>/';

        return preg_match($shortPattern, $docText) === 1
            || preg_match($fullPattern, $docText) === 1
            || preg_match($fullyQualifiedPattern, $docText) === 1;
    }

    private function addAnnotationToTraitUse(Class_ $node, string $builderClass): void
    {
        $traitUse = $this->getHasBuilderTraitUse($node);

        if ($traitUse === null) {
            return;
        }

        $traitUse->setDocComment(new Doc('/** @use HasBuilder<'.$builderClass.'> */'));
    }

    private function getHasBuilderTraitUse(Class_ $node): null|TraitUse
    {
        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof TraitUse) {
                continue;
            }

            foreach ($stmt->traits as $trait) {
                $traitName = ltrim($trait->toString(), '\\');

                if ($traitName === 'HasBuilder' || $traitName === HasBuilder::class) {
                    return $stmt;
                }
            }
        }

        return null;
    }
}
