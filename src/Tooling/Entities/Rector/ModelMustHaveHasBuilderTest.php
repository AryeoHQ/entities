<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use Illuminate\Database\Eloquent\HasBuilder;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Tooling\Concerns\GetsFixtures;
use Tooling\Rector\Rules\Provides\ValidatesInheritance;
use Tooling\Rector\Testing\ParsesNodes;
use Tooling\Rector\Testing\ResolvesRectorRules;

#[CoversClass(ModelMustHaveHasBuilder::class)]
class ModelMustHaveHasBuilderTest extends TestCase
{
    use GetsFixtures;
    use ParsesNodes;
    use ResolvesRectorRules;
    use ValidatesInheritance;

    #[Test]
    public function it_adds_has_builder_trait_and_annotation_when_missing(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ModelWithoutHasBuilder.php'));

        $this->assertFalse($this->inherits($classNode, HasBuilder::class));

        $rule = $this->resolveRule(ModelMustHaveHasBuilder::class);
        $result = $rule->refactor($classNode);

        $this->assertInstanceOf(Class_::class, $result);
        $this->assertTrue($this->inherits($result, HasBuilder::class));
        $this->assertStringContainsString('@use HasBuilder<ValidBuilder>', $this->getHasBuilderDocblock($result));
    }

    #[Test]
    public function it_adds_annotation_when_has_builder_trait_is_present(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ModelWithoutHasBuilderAnnotation.php'));

        $this->assertTrue($this->inherits($classNode, HasBuilder::class));

        $rule = $this->resolveRule(ModelMustHaveHasBuilder::class);
        $result = $rule->refactor($classNode);

        $this->assertInstanceOf(Class_::class, $result);
        $this->assertStringContainsString('@use HasBuilder<ValidBuilder>', $this->getHasBuilderDocblock($result));
    }

    #[Test]
    public function it_does_not_modify_when_has_builder_annotation_already_present(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ValidModel.php'));

        $rule = $this->resolveRule(ModelMustHaveHasBuilder::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }

    #[Test]
    public function it_does_not_modify_when_class_is_not_an_entity(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/BareClass.php'));

        $rule = $this->resolveRule(ModelMustHaveHasBuilder::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }

    private function getHasBuilderDocblock(Class_ $class): string
    {
        foreach ($class->stmts as $stmt) {
            if (! $stmt instanceof TraitUse) {
                continue;
            }

            foreach ($stmt->traits as $trait) {
                if (ltrim($trait->toString(), '\\') === HasBuilder::class || $trait->toString() === 'HasBuilder') {
                    return $stmt->getDocComment()?->getText() ?? '';
                }
            }
        }

        return '';
    }
}
