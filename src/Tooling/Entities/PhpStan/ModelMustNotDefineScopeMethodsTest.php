<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<ModelMustNotDefineScopeMethods> */
#[CoversClass(ModelMustNotDefineScopeMethods::class)]
class ModelMustNotDefineScopeMethodsTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new ModelMustNotDefineScopeMethods;
    }

    #[Test]
    public function it_passes_when_model_has_no_scope_methods(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidModel.php')], []);
    }

    #[Test]
    public function it_passes_when_class_is_not_an_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ClassWithoutEntityDriven.php')], []);
    }

    #[Test]
    public function it_fails_when_model_defines_scope_methods(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ModelWithScopeMethod.php')], [
            [
                'Model must not define scope methods; define scopes on the Builder instead.',
                14,
            ],
            [
                'Model must not define scope methods; define scopes on the Builder instead.',
                20,
            ],
        ]);
    }
}
