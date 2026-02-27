<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<ModelMustHaveHasUuids> */
#[CoversClass(ModelMustHaveHasUuids::class)]
class ModelMustHaveHasUuidsTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new ModelMustHaveHasUuids;
    }

    #[Test]
    public function it_passes_when_entity_uses_has_uuids(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidModel.php')], []);
    }

    #[Test]
    public function it_passes_when_class_does_not_implement_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ClassWithoutEntityDriven.php')], []);
    }

    #[Test]
    public function it_fails_when_entity_does_not_use_has_uuids(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ModelWithoutHasUuids.php')], [
            [
                'Model must use the HasUuids.',
                10,
            ],
        ]);
    }
}
