<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PHPStan\Analyser\Scope;
use Support\Entities\Contracts\Entity;
use Tooling\PhpStan\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Class_>
 */
#[NodeType(Class_::class)]
final class HasFactoryMustHaveGenericAnnotation extends Rule
{
    /**
     * @param  Class_  $node
     */
    public function shouldHandle(Node $node, Scope $scope): bool
    {
        return $this->inherits($node, Model::class)
            && $this->inherits($node, Entity::class)
            && $this->hasDirectHasFactoryWithoutGeneric($node);
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node, Scope $scope): void
    {
        $this->error(
            message: 'HasFactory trait must have a generic type annotation: /** @use HasFactory<Factory> */.',
            line: $this->findHasFactoryLine($node),
            identifier: 'entities.hasFactoryGeneric',
        );
    }

    private function hasDirectHasFactoryWithoutGeneric(Class_ $node): bool
    {
        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof TraitUse) {
                continue;
            }

            foreach ($stmt->traits as $trait) {
                if (! $this->isHasFactory($trait)) {
                    continue;
                }

                $docComment = $stmt->getDocComment();

                return $docComment === null || ! str_contains($docComment->getText(), '@use HasFactory<');
            }
        }

        return false;
    }

    private function findHasFactoryLine(Class_ $node): int
    {
        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof TraitUse) {
                continue;
            }

            foreach ($stmt->traits as $trait) {
                if ($this->isHasFactory($trait)) {
                    return $stmt->getStartLine();
                }
            }
        }

        return $node->getStartLine();
    }

    private function isHasFactory(Node\Name $trait): bool
    {
        $name = ltrim($trait->toString(), '\\');

        return $name === HasFactory::class || $name === 'HasFactory';
    }
}
