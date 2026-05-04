<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PHPStan\Analyser\Scope;
use Support\Entities\Contracts\Entity;
use Tooling\Entities\Concerns\DetectsEloquentBuilder;
use Tooling\PhpStan\Rules\Rule;
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
    public function shouldHandle(Node $node, Scope $scope): bool
    {
        if (! $node instanceof Class_) {
            return false;
        }

        return $this->inherits($node, Model::class)
            && $this->inherits($node, Entity::class)
            && $this->getUseEloquentBuilderClass($node) !== null;
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node, Scope $scope): void
    {
        $builderClass = $this->getUseEloquentBuilderClass($node);

        if ($builderClass === null || $this->hasBuilderTraitWithAnnotation($node, $builderClass)) {
            return;
        }

        $this->error(
            message: sprintf(
                'Model must use %s with a /** @use %s<%s> */ annotation.',
                class_basename(HasBuilder::class),
                class_basename(HasBuilder::class),
                $this->getShortClassName($builderClass),
            ),
            line: $node->name?->getStartLine() ?? $node->getStartLine(),
            identifier: 'entities.Model.HasBuilder.required',
        );
    }

    private function hasBuilderTraitWithAnnotation(Class_ $node, string $builderClass): bool
    {
        $shortBuilderClass = $this->getShortClassName($builderClass);

        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof TraitUse) {
                continue;
            }

            foreach ($stmt->traits as $trait) {
                if ($trait->toString() !== 'HasBuilder' && $trait->toString() !== HasBuilder::class) {
                    continue;
                }

                $docComment = $stmt->getDocComment();

                if ($docComment === null) {
                    return false;
                }

                $docText = $docComment->getText();
                $shortPattern = '/@use\s+HasBuilder<'.preg_quote($shortBuilderClass, '/').'>/';
                $fullPattern = '/@use\s+HasBuilder<'.preg_quote($builderClass, '/').'>/';
                $fullyQualifiedPattern = '/@use\s+HasBuilder<\\\\'.preg_quote($builderClass, '/').'>/';

                return preg_match($shortPattern, $docText) === 1
                    || preg_match($fullPattern, $docText) === 1
                    || preg_match($fullyQualifiedPattern, $docText) === 1;
            }
        }

        return false;
    }
}
