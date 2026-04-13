<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\TraitUse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Tooling\Concerns\GetsFixtures;
use Tooling\Rector\Testing\ParsesNodes;
use Tooling\Rector\Testing\ResolvesRectorRules;

#[CoversClass(HasFactoryMustHaveGenericAnnotation::class)]
class HasFactoryMustHaveGenericAnnotationTest extends TestCase
{
    use GetsFixtures;
    use ParsesNodes;
    use ResolvesRectorRules;

    #[Test]
    public function it_adds_generic_annotation_when_missing(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ModelWithoutHasFactoryGeneric.php'));

        $rule = $this->resolveRule(HasFactoryMustHaveGenericAnnotation::class);
        $result = $rule->refactor($classNode);

        $this->assertInstanceOf(Class_::class, $result);

        $traitUse = $this->findHasFactoryTraitUse($result);
        $this->assertNotNull($traitUse);

        $docComment = $traitUse->getDocComment();
        $this->assertNotNull($docComment);
        $this->assertStringContainsString('@use HasFactory<', $docComment->getText());
    }

    #[Test]
    public function it_does_not_modify_when_generic_annotation_already_present(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ValidModel.php'));

        $rule = $this->resolveRule(HasFactoryMustHaveGenericAnnotation::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }

    #[Test]
    public function it_does_not_modify_when_class_is_not_an_entity(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ModelWithoutEntity.php'));

        $rule = $this->resolveRule(HasFactoryMustHaveGenericAnnotation::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }

    private function findHasFactoryTraitUse(Class_ $node): null|TraitUse
    {
        foreach ($node->stmts as $stmt) {
            if (! $stmt instanceof TraitUse) {
                continue;
            }

            foreach ($stmt->traits as $trait) {
                if (str_ends_with($trait->toString(), 'HasFactory')) {
                    return $stmt;
                }
            }
        }

        return null;
    }
}
