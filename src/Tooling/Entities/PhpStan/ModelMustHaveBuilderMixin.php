<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Support\Entities\Contracts\Entity;
use Tooling\Entities\Concerns\DetectsEloquentBuilder;
use Tooling\PhpStan\Rules\Rule;
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

        if ($builderClass === null || $this->hasBuilderMixin($node, $builderClass)) {
            return;
        }

        $this->error(
            message: sprintf('Model must have @mixin %s in the class docblock.', $this->getShortClassName($builderClass)),
            line: $node->name?->getStartLine() ?? $node->getStartLine(),
            identifier: 'entities.Model.BuilderMixin.required',
        );
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
}
