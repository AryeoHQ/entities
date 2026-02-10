<?php

declare(strict_types=1);

namespace Tests\Tooling\Entities\PhpStan;

use Illuminate\Database\Eloquent\Model;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tooling\Entities\PhpStan\ModelMustImplementEntity;

/** @extends RuleTestCase<ModelMustImplementEntity> */
#[CoversClass(ModelMustImplementEntity::class)]
class ModelMustImplementEntityTest extends RuleTestCase
{
    protected function getRule(): ModelMustImplementEntity
    {
        return new ModelMustImplementEntity($this->createReflectionProvider());
    }

    private function getFixturePath(string $filename): string
    {
        return __DIR__.'/../../../Fixtures/Tooling/Entities/'.$filename;
    }

    #[Test]
    public function it_passes_when_model_implements_entity_interface(): void
    {
        $this->analyse([$this->getFixturePath('ValidModel.php')], []);
    }

    #[Test]
    public function it_passes_when_model_extends_model_with_entity(): void
    {
        $this->analyse([
            $this->getFixturePath('ValidModel.php'),
            $this->getFixturePath('ValidExtendedModel.php'),
        ], []);
    }

    #[Test]
    public function it_fails_when_model_does_not_implement_entity_interface(): void
    {
        $this->analyse([$this->getFixturePath('ModelWithoutEntity.php')], [
            [
                Model::class.' must implement `Entity` contract.',
                9,
            ],
        ]);
    }
}
