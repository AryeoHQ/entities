<?php

declare(strict_types=1);

namespace Support\Entities\Events\Provides;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Support\Entities\Contracts\Entity;
use Support\Entities\Events\IdentifiesEntity\Exceptions\MultipleDefined;
use Support\Entities\Events\IdentifiesEntity\Exceptions\NotDefined;
use Support\Entities\Events\IdentifiesEntity\Exceptions\NotEntity;
use Support\Entities\Events\IdentifiesEntity\IdentifiesEntity;

trait HasEntity
{
    /**
     * This is PURPOSELY NOT memoized to guarantee it
     * is not included when the event is serialized
     */
    public Entity $entity {
        get => $this->{$this->entityProperty};
    }

    /**
     * This is PURPOSELY initialized as an empty string to guarantee
     * it will be included when the event is serialized as PHP will
     * automatically include all initialized properties. However,
     * we never want the value to actually be an empty string.
     * Since we are defining a get hook, that is ALWAYS called
     * no matter how a property is accessed. Since serialization
     * necessarily reads "initialized" properties we can ensure
     * that even if this property was never accessed during the
     * lifecycle of the application the evaluation will be run.
     */
    private string $entityProperty = '' {
        get => $this->entityProperty ?: $this->entityProperty = with( // @phpstan-ignore ternary.shortNotAllowed
            collect((new ReflectionClass($this))->getProperties())
                ->filter(fn (ReflectionProperty $property): bool => (bool) $property->getAttributes(IdentifiesEntity::class))
                ->tap(fn ($properties) => throw_unless($properties->isNotEmpty(), NotDefined::class, $this))
                ->tap(fn ($properties) => throw_unless($properties->count() === 1, MultipleDefined::class, $this))
                ->first(),
            function (ReflectionProperty $property) {
                throw_unless($property->getType() instanceof ReflectionNamedType, NotEntity::class, $this);
                throw_unless(is_subclass_of($property->getType()->getName(), Entity::class), NotEntity::class, $this);

                return $property->getName();
            }
        );
    }
}
