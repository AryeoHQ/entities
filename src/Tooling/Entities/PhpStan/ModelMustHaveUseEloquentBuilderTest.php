<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<ModelMustHaveUseEloquentBuilder> */
#[CoversClass(ModelMustHaveUseEloquentBuilder::class)]
class ModelMustHaveUseEloquentBuilderTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new ModelMustHaveUseEloquentBuilder;
    }

    #[Test]
    public function it_passes_when_model_has_use_eloquent_builder(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidModel.php')], []);
    }

    #[Test]
    public function it_passes_when_class_is_not_an_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/BareClass.php')], []);
    }

    #[Test]
    public function it_fails_when_model_does_not_have_use_eloquent_builder(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ModelWithoutUseEloquentBuilder.php')], [
            [
                sprintf('Model must be annotated with #[%s].', class_basename(UseEloquentBuilder::class)),
                12,
            ],
        ]);
    }
}
