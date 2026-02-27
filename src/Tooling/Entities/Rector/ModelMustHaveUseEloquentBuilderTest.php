<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use PhpParser\Node\Stmt\Class_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Tooling\Concerns\GetsFixtures;
use Tooling\Rector\Rules\Provides\ValidatesAttributes;
use Tooling\Rector\Testing\ParsesNodes;
use Tooling\Rector\Testing\ResolvesRectorRules;

#[CoversClass(ModelMustHaveUseEloquentBuilder::class)]
class ModelMustHaveUseEloquentBuilderTest extends TestCase
{
    use GetsFixtures;
    use ParsesNodes;
    use ResolvesRectorRules;
    use ValidatesAttributes;

    #[Test]
    public function it_adds_use_eloquent_builder_attribute_when_missing(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ModelWithoutUseEloquentBuilder.php'));

        $this->assertFalse($this->hasAttribute($classNode, UseEloquentBuilder::class));

        $rule = $this->resolveRule(ModelMustHaveUseEloquentBuilder::class);
        $result = $rule->refactor($classNode);

        $this->assertInstanceOf(Class_::class, $result);
        $this->assertTrue($this->hasAttribute($result, UseEloquentBuilder::class));
    }

    #[Test]
    public function it_does_not_modify_when_use_eloquent_builder_already_present(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ValidModel.php'));

        $rule = $this->resolveRule(ModelMustHaveUseEloquentBuilder::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }

    #[Test]
    public function it_does_not_modify_when_class_is_not_an_entity(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ModelWithoutEntity.php'));

        $rule = $this->resolveRule(ModelMustHaveUseEloquentBuilder::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }
}
