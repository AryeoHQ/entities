<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<ModelMustHaveUseFactory> */
#[CoversClass(ModelMustHaveUseFactory::class)]
class ModelMustHaveUseFactoryTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new ModelMustHaveUseFactory;
    }

    #[Test]
    public function it_passes_when_model_has_use_factory(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidModel.php')], []);
    }

    #[Test]
    public function it_passes_when_class_is_not_an_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/BareClass.php')], []);
    }

    #[Test]
    public function it_fails_when_model_does_not_have_use_factory(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ModelWithoutUseFactory.php')], [
            [
                sprintf('Model must be annotated with #[%s].', class_basename(UseFactory::class)),
                12,
            ],
        ]);
    }
}
