<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<HasFactoryMustHaveGenericAnnotation> */
#[CoversClass(HasFactoryMustHaveGenericAnnotation::class)]
class HasFactoryMustHaveGenericAnnotationTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new HasFactoryMustHaveGenericAnnotation;
    }

    #[Test]
    public function it_passes_when_has_factory_has_generic_annotation(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidModel.php')], []);
    }

    #[Test]
    public function it_passes_when_class_is_not_an_entity(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ClassWithoutEntityDriven.php')], []);
    }

    #[Test]
    public function it_fails_when_has_factory_is_missing_generic_annotation(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ModelWithoutHasFactoryGeneric.php')], [
            [
                'HasFactory trait must have a generic type annotation: /** @use HasFactory<Factory> */.',
                15,
            ],
        ]);
    }
}
