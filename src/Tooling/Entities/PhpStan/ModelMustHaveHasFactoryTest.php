<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<ModelMustHaveHasFactory> */
#[CoversClass(ModelMustHaveHasFactory::class)]
class ModelMustHaveHasFactoryTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new ModelMustHaveHasFactory;
    }

    #[Test]
    public function it_passes_when_model_uses_has_factory(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidModel.php')], []);
    }

    #[Test]
    public function it_passes_when_class_is_not_an_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/BareClass.php')], []);
    }

    #[Test]
    public function it_fails_when_model_does_not_use_has_factory(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ModelWithoutHasFactory.php')], [
            [
                sprintf('Model must use %s.', class_basename(HasFactory::class)),
                10,
            ],
        ]);
    }
}
