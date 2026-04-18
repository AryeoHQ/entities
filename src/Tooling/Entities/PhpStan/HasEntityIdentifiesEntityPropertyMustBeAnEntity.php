<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Support\Collection;
use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ObjectType;
use Support\Entities\Contracts\Entity;
use Support\Entities\Events\IdentifiesEntity\IdentifiesEntity;
use Support\Entities\Events\Provides\HasEntity;
use Tooling\PhpStan\Rules\Rule;
use Tooling\Rules\Attributes\NodeType;

/**
 * @extends Rule<Class_>
 */
#[NodeType(Class_::class)]
class HasEntityIdentifiesEntityPropertyMustBeAnEntity extends Rule
{
    /** @var Collection<int, Property|Param> */
    private Collection $invalidProperties;

    public function prepare(Node $node, Scope $scope): void
    {
        $properties = collect($node->stmts)
            ->filter(fn (Node\Stmt $stmt): bool => $stmt instanceof Property)
            ->filter(fn (Property $property): bool => $this->hasAttribute($property, IdentifiesEntity::class))
            ->filter(fn (Property $property): bool => ! $this->isValidEntityType($property));

        $promotedProperties = collect($node->stmts)
            ->filter(fn (Node\Stmt $stmt): bool => $stmt instanceof ClassMethod && $stmt->name->toString() === '__construct')
            ->flatMap(fn (ClassMethod $method) => $method->params)
            ->filter(fn (Param $param): bool => $param->flags !== 0 && $this->hasAttribute($param, IdentifiesEntity::class))
            ->filter(fn (Param $param): bool => ! $this->isValidEntityType($param));

        $this->invalidProperties = $properties->merge($promotedProperties);
    }

    public function shouldHandle(Node $node, Scope $scope): bool
    {
        return $this->inherits($node, HasEntity::class)
            && $this->invalidProperties->isNotEmpty();
    }

    public function handle(Node $node, Scope $scope): void
    {
        $this->invalidProperties->each(
            fn (Property|Param $property) => $this->error(
                sprintf(
                    'Property with #[%s] attribute must be an %s, %s given.',
                    class_basename(IdentifiesEntity::class),
                    class_basename(Entity::class),
                    $property->type?->toString() ?? 'null'
                ),
                $this->getPropertyLine($property),
                'entities.HasEntity.IdentifiesEntity.mustBeEntity'
            )
        );
    }

    private function isValidEntityType(Property|Param $property): bool
    {
        if ($property->type === null) {
            return false;
        }

        $typeName = $property->type->toString();
        $propertyType = new ObjectType($typeName);
        $entityType = new ObjectType(Entity::class);

        return $entityType->isSuperTypeOf($propertyType)->yes();
    }

    private function getPropertyLine(Property|Param $property): int
    {
        if ($property instanceof Param) {
            return $property->var->getStartLine();
        }

        return data_get($property, 'props.0.name')?->getStartLine() ?? $property->getStartLine();
    }
}
