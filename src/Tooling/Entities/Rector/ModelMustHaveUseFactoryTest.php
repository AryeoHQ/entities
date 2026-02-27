<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use PhpParser\Node\Stmt\Class_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Tooling\Concerns\GetsFixtures;
use Tooling\Rector\Rules\Provides\ValidatesAttributes;
use Tooling\Rector\Testing\ParsesNodes;
use Tooling\Rector\Testing\ResolvesRectorRules;

#[CoversClass(ModelMustHaveUseFactory::class)]
class ModelMustHaveUseFactoryTest extends TestCase
{
    use GetsFixtures;
    use ParsesNodes;
    use ResolvesRectorRules;
    use ValidatesAttributes;

    #[Test]
    public function it_adds_use_factory_attribute_when_missing(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ModelWithoutUseFactory.php'));

        $this->assertFalse($this->hasAttribute($classNode, UseFactory::class));

        $rule = $this->resolveRule(ModelMustHaveUseFactory::class);
        $result = $rule->refactor($classNode);

        $this->assertInstanceOf(Class_::class, $result);
        $this->assertTrue($this->hasAttribute($result, UseFactory::class));
    }

    #[Test]
    public function it_does_not_modify_when_use_factory_already_present(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ValidModel.php'));

        $rule = $this->resolveRule(ModelMustHaveUseFactory::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }

    #[Test]
    public function it_does_not_modify_when_class_is_not_an_entity(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ModelWithoutEntity.php'));

        $rule = $this->resolveRule(ModelMustHaveUseFactory::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }
}
