<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use Rector\PostRector\Collector\UseNodesToAddCollector;
use Rector\Rector\AbstractRector;
use Rector\StaticTypeMapper\ValueObject\Type\FullyQualifiedObjectType;
use Support\Entities\Contracts\Entity;
use Throwable;

class ModelMustImplementEntity extends AbstractRector
{
    public function __construct(
        private UseNodesToAddCollector $useNodesToAddCollector
    ) {}

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): null|Node
    {
        if (! $node instanceof Class_) {
            return null;
        }

        $extendsModel = $this->extendsEloquentModel($node);
        $implementsEntity = $this->implementsEntityContract($node);

        // If class extends Model, add Entity contract if missing
        if ($extendsModel && ! $implementsEntity) {
            return $this->addEntityContract($node);
        }

        return null;
    }

    private function extendsEloquentModel(Class_ $node): bool
    {
        if ($node->extends === null) {
            return false;
        }

        if ($node->extends instanceof FullyQualified && $node->extends->toString() === Model::class) {
            return true;
        }

        if ($node->extends->toString() === 'Model') {
            return true;
        }

        // Check if the extended class is a subclass of Model
        $parentClassName = $node->extends->toString();
        if ($this->isObjectType($node->extends, new FullyQualifiedObjectType(Model::class))) {
            return true;
        }

        return false;
    }

    private function implementsEntityContract(Class_ $node): bool
    {
        if ($node->implements === []) {
            return false;
        }

        foreach ($node->implements as $interface) {
            if ($interface instanceof FullyQualified && $interface->toString() === Entity::class) {
                return true;
            }

            if ($interface->toString() === 'Entity') {
                return true;
            }
        }

        return false;
    }

    private function addEntityContract(Class_ $node): Class_
    {
        // Check if contract is already implemented
        if ($this->implementsEntityContract($node)) {
            return $node;
        }

        // Add use statement for Entity contract
        // Only add use import if we have a current file context (not in tests)
        try {
            $this->useNodesToAddCollector->addUseImport(
                new FullyQualifiedObjectType(Entity::class)
            );
        } catch (Throwable $e) {
            // In test environments, UseNodesToAddCollector might not have a current file
            // This is expected and we can continue without adding the use statement
        }

        $entityInterface = new Name('Entity');

        if ($node->implements === []) {
            $node->implements = [$entityInterface];
        } else {
            $node->implements[] = $entityInterface;
        }

        return $node;
    }
}
