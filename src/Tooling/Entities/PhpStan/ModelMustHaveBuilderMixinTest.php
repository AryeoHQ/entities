<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<ModelMustHaveBuilderMixin> */
#[CoversClass(ModelMustHaveBuilderMixin::class)]
class ModelMustHaveBuilderMixinTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new ModelMustHaveBuilderMixin;
    }

    #[Test]
    public function it_passes_when_model_has_builder_mixin(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidModel.php')], []);
    }

    #[Test]
    public function it_passes_when_class_is_not_an_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/BareClass.php')], []);
    }

    #[Test]
    public function it_fails_when_model_does_not_have_builder_mixin(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ModelWithoutBuilderMixin.php')], [
            [
                'Model must have @mixin ValidBuilder in the class docblock.',
                13,
            ],
        ]);
    }
}
