<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Events\Contracts\ForEntity;
use Support\Entities\Events\Provides\HasEntity;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<HasEntityMustImplementForEntity> */
#[CoversClass(HasEntityMustImplementForEntity::class)]
class HasEntityMustImplementForEntityTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new HasEntityMustImplementForEntity;
    }

    #[Test]
    public function it_passes_when_has_entity_implements_for_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidForEntityEvent.php')], []);
    }

    #[Test]
    public function it_passes_when_class_does_not_use_has_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/BareClass.php')], []);
    }

    #[Test]
    public function it_fails_when_has_entity_does_not_implement_for_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/HasEntityWithoutForEntity.php')], [
            [
                sprintf(
                    '%s must implement %s.',
                    class_basename(HasEntity::class),
                    class_basename(ForEntity::class)
                ),
                11,
            ],
        ]);
    }
}
