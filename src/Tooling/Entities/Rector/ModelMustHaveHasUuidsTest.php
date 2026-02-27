<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use PhpParser\Node\Stmt\Class_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Tooling\Concerns\GetsFixtures;
use Tooling\Rector\Rules\Provides\ValidatesInheritance;
use Tooling\Rector\Testing\ParsesNodes;
use Tooling\Rector\Testing\ResolvesRectorRules;

#[CoversClass(ModelMustHaveHasUuids::class)]
class ModelMustHaveHasUuidsTest extends TestCase
{
    use GetsFixtures;
    use ParsesNodes;
    use ResolvesRectorRules;
    use ValidatesInheritance;

    #[Test]
    public function it_adds_has_uuids_trait_when_missing(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ModelWithoutHasUuids.php'));

        $this->assertFalse($this->inherits($classNode, HasUuids::class));

        $rule = $this->resolveRule(ModelMustHaveHasUuids::class);
        $result = $rule->refactor($classNode);

        $this->assertInstanceOf(Class_::class, $result);
        $this->assertTrue($this->inherits($result, HasUuids::class));
    }

    #[Test]
    public function it_does_not_modify_when_has_uuids_already_present(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ValidModel.php'));

        $rule = $this->resolveRule(ModelMustHaveHasUuids::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }

    #[Test]
    public function it_does_not_modify_when_class_is_not_an_entity(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ModelWithoutEntity.php'));

        $rule = $this->resolveRule(ModelMustHaveHasUuids::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }
}
