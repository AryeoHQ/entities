<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Contracts\Entity;
use Support\Entities\Events\Entity\IdentifiesEntity;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<HasEntityIdentifiesEntityPropertyMustBeAnEntity> */
#[CoversClass(HasEntityIdentifiesEntityPropertyMustBeAnEntity::class)]
class HasEntityIdentifiesEntityPropertyMustBeAnEntityTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new HasEntityIdentifiesEntityPropertyMustBeAnEntity;
    }

    #[Test]
    public function it_passes_when_entity_property_implements_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidForEntityEvent.php')], []);
    }

    #[Test]
    public function it_passes_when_class_has_no_entity_attribute(): void
    {
        $this->analyse([$this->getFixturePath('Entities/BareClass.php')], []);
    }

    #[Test]
    public function it_fails_when_entity_property_is_not_an_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/HasEntityWithInvalidIdentifiesEntityType.php')], [
            [
                sprintf(
                    'Property with #[%s] attribute must be an %s, %s given.',
                    class_basename(IdentifiesEntity::class),
                    class_basename(Entity::class),
                    'string'
                ),
                16,
            ],
        ]);
    }

    #[Test]
    public function it_fails_when_promoted_param_is_not_an_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/HasEntityWithInvalidIdentifiesEntityPromotedInvalidType.php')], [
            [
                sprintf(
                    'Property with #[%s] attribute must be an %s, %s given.',
                    class_basename(IdentifiesEntity::class),
                    class_basename(Entity::class),
                    'string'
                ),
                17,
            ],
        ]);
    }
}
