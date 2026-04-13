<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use Support\Entities\Contracts\Entity;
use Tooling\Rector\Rules\Rule;
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
    public function shouldHandle(Node $node): bool
    {
        return $this->inherits($node, Model::class)
            && $this->inherits($node, Entity::class)
            && $this->hasDirectHasFactoryWithoutGeneric($node);
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node): null|Node
    {
        $traitUse = $this->findHasFactoryTraitUse($node);

        if ($traitUse === null) {
            return null;
        }

        $factoryFqcn = $this->deriveFactoryFqcn($node);

        $traitUse->setDocComment(new Doc(
            sprintf('/** @use HasFactory<\%s> */', $factoryFqcn),
        ));

        return $node;
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

    private function findHasFactoryTraitUse(Class_ $node): null|TraitUse
    {
        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof TraitUse) {
                continue;
            }

            foreach ($stmt->traits as $trait) {
                if ($this->isHasFactory($trait)) {
                    return $stmt;
                }
            }
        }

        return null;
    }

    private function isHasFactory(Node\Name $trait): bool
    {
        $name = ltrim($trait->toString(), '\\');

        return $name === HasFactory::class || $name === 'HasFactory';
    }

    private function deriveFactoryFqcn(Class_ $node): string
    {
        $namespace = $node->namespacedName?->slice(0, -1)?->toString() ?? '';

        return $namespace.'\\Factory\\Factory';
    }
}
