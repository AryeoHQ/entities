<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use Support\Entities\Events\Entity\IdentifiesEntity;
use Support\Entities\Events\Provides\HasEntity;
use Tooling\PhpStan\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Class_>
 */
#[NodeType(Class_::class)]
class HasEntityCannotHaveMultipleIdentifiesEntityProperties extends Rule
{
    /** @var \Illuminate\Support\Collection<int, Property|Param> */
    private \Illuminate\Support\Collection $identifiesEntityProperties;

    public function prepare(Node $node, Scope $scope): void
    {
        $properties = collect($node->stmts)
            ->filter(fn (Node\Stmt $stmt): bool => $stmt instanceof Property)
            ->filter(fn (Property $property): bool => $this->hasAttribute($property, IdentifiesEntity::class));

        $promotedProperties = collect($node->stmts)
            ->filter(fn (Node\Stmt $stmt): bool => $stmt instanceof ClassMethod && $stmt->name->toString() === '__construct')
            ->flatMap(fn (ClassMethod $method) => $method->params)
            ->filter(fn (Param $param): bool => $param->flags !== 0 && $this->hasAttribute($param, IdentifiesEntity::class));

        $this->identifiesEntityProperties = $properties->merge($promotedProperties);
    }

    public function shouldHandle(Node $node, Scope $scope): bool
    {
        return $this->inherits($node, HasEntity::class)
            && $this->identifiesEntityProperties->count() > 1;
    }

    public function handle(Node $node, Scope $scope): void
    {
        $this->identifiesEntityProperties->each(
            fn (Property|Param $property) => $this->error(
                sprintf(
                    '%s must have only one property with the #[%s] attribute.',
                    class_basename(HasEntity::class),
                    class_basename(IdentifiesEntity::class)
                ),
                $this->getPropertyLine($property),
                'entities.HasEntity.IdentifiesEntity.multipleNotAllowed'
            )
        );
    }

    private function getPropertyLine(Property|Param $property): int
    {
        if ($property instanceof Param) {
            return $property->var->getStartLine();
        }

        return data_get($property, 'props.0.name')?->getStartLine() ?? $property->getStartLine();
    }
}
