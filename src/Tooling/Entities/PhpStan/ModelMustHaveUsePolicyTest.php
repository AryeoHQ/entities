<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<ModelMustHaveUsePolicy> */
#[CoversClass(ModelMustHaveUsePolicy::class)]
class ModelMustHaveUsePolicyTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new ModelMustHaveUsePolicy;
    }

    #[Test]
    public function it_passes_when_model_has_use_policy(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidModel.php')], []);
    }

    #[Test]
    public function it_passes_when_class_is_not_an_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ClassWithoutEntityDriven.php')], []);
    }

    #[Test]
    public function it_fails_when_model_does_not_have_use_policy(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ModelWithoutUsePolicy.php')], [
            [
                'Model must have a #[UsePolicy] attribute.',
                12,
            ],
        ]);
    }
}
