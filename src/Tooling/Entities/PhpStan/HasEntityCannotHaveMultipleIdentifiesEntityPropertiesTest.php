<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Events\IdentifiesEntity\IdentifiesEntity;
use Support\Entities\Events\Provides\HasEntity;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<HasEntityCannotHaveMultipleIdentifiesEntityProperties> */
#[CoversClass(HasEntityCannotHaveMultipleIdentifiesEntityProperties::class)]
class HasEntityCannotHaveMultipleIdentifiesEntityPropertiesTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new HasEntityCannotHaveMultipleIdentifiesEntityProperties;
    }

    #[Test]
    public function it_passes_when_single_identifies_entity_property(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidForEntityEvent.php')], []);
    }

    #[Test]
    public function it_passes_when_class_has_no_entity_attribute(): void
    {
        $this->analyse([$this->getFixturePath('Entities/BareClass.php')], []);
    }

    #[Test]
    public function it_fails_when_multiple_identifies_entity_properties(): void
    {
        $message = sprintf(
            '%s must have only one property with the #[%s] attribute.',
            class_basename(HasEntity::class),
            class_basename(IdentifiesEntity::class)
        );

        $this->analyse([$this->getFixturePath('Entities/HasEntityWithMultipleIdentifiesEntityAttributes.php')], [
            [$message, 17],
            [$message, 20],
        ]);
    }

    #[Test]
    public function it_fails_when_multiple_identifies_entity_promoted_params(): void
    {
        $message = sprintf(
            '%s must have only one property with the #[%s] attribute.',
            class_basename(HasEntity::class),
            class_basename(IdentifiesEntity::class)
        );

        $this->analyse([$this->getFixturePath('Entities/HasEntityWithMultipleIdentifiesEntityPromoted.php')], [
            [$message, 18],
            [$message, 20],
        ]);
    }
}
