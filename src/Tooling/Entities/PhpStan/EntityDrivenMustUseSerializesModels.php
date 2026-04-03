<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Trait_;
use PHPStan\Analyser\Scope;
use Support\Entities\Events\Concerns\SerializesModels;
use Support\Entities\Events\Provides\EntityDriven;
use Tooling\PhpStan\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Trait_>
 */
#[NodeType(Trait_::class)]
final class EntityDrivenMustUseSerializesModels extends Rule
{
    /**
     * @param  Trait_  $node
     */
    public function shouldHandle(Node $node, Scope $scope): bool
    {
        return $node->namespacedName?->toString() === EntityDriven::class
            && $this->doesNotInherit($node, SerializesModels::class);
    }

    /**
     * @param  Trait_  $node
     */
    public function handle(Node $node, Scope $scope): void
    {
        $this->error(
            '`EntityDriven` must use `SerializesModels`.',
            $node->getStartLine(),
            'entities.entityDriven.serializesModels',
        );
    }
}
