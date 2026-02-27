<?php

declare(strict_types=1);

namespace Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\Model;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Tooling\Concerns\GetsFixtures;

/** @extends RuleTestCase<ModelMustImplementEntity> */
#[CoversClass(ModelMustImplementEntity::class)]
class ModelMustImplementEntityTest extends RuleTestCase
{
    use GetsFixtures;

    protected function getRule(): Rule
    {
        return new ModelMustImplementEntity;
    }

    #[Test]
    public function it_passes_when_model_implements_entity_interface(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ValidModel.php')], []);
    }

    #[Test]
    public function it_passes_when_model_extends_model_with_entity(): void
    {
        $this->analyse([
            $this->getFixturePath('Entities/ValidModel.php'),
            $this->getFixturePath('Entities/ValidExtendedModel.php'),
        ], []);
    }

    #[Test]
    public function it_fails_when_model_does_not_implement_entity_interface(): void
    {
        $this->analyse([$this->getFixturePath('Entities/ModelWithoutEntity.php')], [
            [
                Model::class.' must implement `Entity` contract.',
                9,
            ],
        ]);
    }
}
