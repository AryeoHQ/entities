<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Provides\HasEntity;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends \Tooling\PhpStan\Rules\Rule<Class_>
 */
#[NodeType(Class_::class)]
final class HasEntityMustImplementForEntity extends \Tooling\PhpStan\Rules\Rule
{
    /**
     * @param  Class_  $node
     */
    public function shouldHandle(Node $node, Scope $scope): bool
    {
        return $this->inherits($node, HasEntity::class)
            && $this->doesNotInherit($node, ForEntity::class);
    }

    /**
     * @param  Class_  $node
     */
    public function handle(Node $node, Scope $scope): void
    {
        $traitLine = $this->getHasEntityTraitLine($node);

        $this->error(
            sprintf(
                '%s must implement %s.',
                class_basename(HasEntity::class),
                class_basename(ForEntity::class)
            ),
            $traitLine,
            'entities.HasEntity.ForEntity.required'
        );
    }

    private function getHasEntityTraitLine(Class_ $node): null|int
    {
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof Node\Stmt\TraitUse) {
                foreach ($stmt->traits as $trait) {
                    if ($trait instanceof FullyQualified) {
                        if ($trait->toString() === HasEntity::class) {
                            return $stmt->getStartLine();
                        }
                    }
                }
            }
        }

        return null;
    }
}
