<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use PhpParser\Node\Stmt\Class_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Tooling\Concerns\GetsFixtures;
use Tooling\Rector\Testing\ParsesNodes;
use Tooling\Rector\Testing\ResolvesRectorRules;

#[CoversClass(ModelMustHaveBuilderMixin::class)]
class ModelMustHaveBuilderMixinTest extends TestCase
{
    use GetsFixtures;
    use ParsesNodes;
    use ResolvesRectorRules;

    #[Test]
    public function it_adds_builder_mixin_when_missing(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ModelWithoutBuilderMixin.php'));

        $this->assertStringNotContainsString('@mixin ValidBuilder', $classNode->getDocComment()?->getText() ?? '');

        $rule = $this->resolveRule(ModelMustHaveBuilderMixin::class);
        $result = $rule->refactor($classNode);

        $this->assertInstanceOf(Class_::class, $result);
        $this->assertStringContainsString('@mixin ValidBuilder', $result->getDocComment()?->getText() ?? '');
    }

    #[Test]
    public function it_does_not_modify_when_builder_mixin_already_present(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ValidModel.php'));

        $rule = $this->resolveRule(ModelMustHaveBuilderMixin::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }

    #[Test]
    public function it_does_not_modify_when_class_is_not_an_entity(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/BareClass.php'));

        $rule = $this->resolveRule(ModelMustHaveBuilderMixin::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }
}
