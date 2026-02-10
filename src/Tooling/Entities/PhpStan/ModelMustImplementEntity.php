<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use Support\Entities\Contracts\Entity;

/**
 * @implements Rule<Class_>
 */
final class ModelMustImplementEntity implements Rule
{
    public function __construct(
        private readonly ReflectionProvider $reflectionProvider
    ) {}

    public function getNodeType(): string
    {
        return Class_::class;
    }

    /**
     * @param  Class_  $node
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $extendsLine = $this->getModelExtensionLine($node, $scope);

        if ($extendsLine === null) {
            return [];
        }

        if ($this->implementsEntityContract($node, $scope)) {
            return [];
        }

        return [
            RuleErrorBuilder::message(Model::class.' must implement `Entity` contract.')
                ->line($extendsLine)
                ->identifier('entities.interface')
                ->build(),
        ];
    }

    private function getModelExtensionLine(Class_ $node, Scope $scope): null|int
    {
        if ($node->extends === null) {
            return null;
        }

        if ($node->extends instanceof FullyQualified) {
            $extendedClass = $node->extends->toString();

            // Check if it extends Model directly
            if ($extendedClass === Model::class) {
                return $node->extends->getStartLine();
            }

            // Check if the extended class is a subclass of Model
            if ($this->reflectionProvider->hasClass($extendedClass)) {
                $extendedReflection = $this->reflectionProvider->getClass($extendedClass);
                if ($this->reflectionProvider->hasClass(Model::class)) {
                    $modelReflection = $this->reflectionProvider->getClass(Model::class);
                    if ($extendedReflection->isSubclassOfClass($modelReflection)) {
                        return $node->extends->getStartLine();
                    }
                }
            }
        }

        return null;
    }

    private function implementsEntityContract(Class_ $node, Scope $scope): bool
    {
        // Check direct implements clause
        foreach ($node->implements as $interface) {
            if ($interface instanceof FullyQualified) {
                if ($interface->toString() === Entity::class) {
                    return true;
                }
            }
        }

        // Check if parent class implements Entity
        if ($node->extends instanceof FullyQualified) {
            $extendedClass = $node->extends->toString();
            if ($this->reflectionProvider->hasClass($extendedClass)) {
                $parentReflection = $this->reflectionProvider->getClass($extendedClass);
                if ($parentReflection->implementsInterface(Entity::class)) {
                    return true;
                }
            }
        }

        return false;
    }
}
