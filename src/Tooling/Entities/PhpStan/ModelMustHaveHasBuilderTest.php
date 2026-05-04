<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\HasBuilder;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<ModelMustHaveHasBuilder> */
#[CoversClass(ModelMustHaveHasBuilder::class)]
class ModelMustHaveHasBuilderTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new ModelMustHaveHasBuilder;
    }

    #[Test]
    public function it_passes_when_model_uses_has_builder_with_annotation(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidModel.php')], []);
    }

    #[Test]
    public function it_passes_when_class_is_not_an_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/BareClass.php')], []);
    }

    #[Test]
    public function it_fails_when_model_does_not_use_has_builder(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ModelWithoutHasBuilder.php')], [
            [
                sprintf('Model must use %s with a /** @use %s<ValidBuilder> */ annotation.', class_basename(HasBuilder::class), class_basename(HasBuilder::class)),
                15,
            ],
        ]);
    }

    #[Test]
    public function it_fails_when_model_does_not_have_has_builder_annotation(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ModelWithoutHasBuilderAnnotation.php')], [
            [
                sprintf('Model must use %s with a /** @use %s<ValidBuilder> */ annotation.', class_basename(HasBuilder::class), class_basename(HasBuilder::class)),
                16,
            ],
        ]);
    }
}
