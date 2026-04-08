<?php

declare(strict_types=1);

namespace Tooling\Entities\Rector;

use PhpParser\Node\Stmt\Class_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Events\Attributes\Alias;
use Tests\TestCase;
use Tests\Tooling\Concerns\GetsFixtures;
use Tooling\Rector\Rules\Provides\ValidatesAttributes;
use Tooling\Rector\Testing\ParsesNodes;
use Tooling\Rector\Testing\ResolvesRectorRules;

#[CoversClass(ForEntityMustHaveAlias::class)]
class ForEntityMustHaveAliasTest extends TestCase
{
    use GetsFixtures;
    use ParsesNodes;
    use ResolvesRectorRules;
    use ValidatesAttributes;

    #[Test]
    public function it_adds_alias_attribute_when_missing(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ForEntityWithoutAlias.php'));

        $this->assertFalse($this->hasAttribute($classNode, Alias::class));

        $rule = $this->resolveRule(ForEntityMustHaveAlias::class);
        $result = $rule->refactor($classNode);

        $this->assertInstanceOf(Class_::class, $result);
        $this->assertTrue($this->hasAttribute($result, Alias::class));
    }

    #[Test]
    public function it_does_not_modify_when_alias_already_present(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ValidEntityEvent.php'));

        $rule = $this->resolveRule(ForEntityMustHaveAlias::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }

    #[Test]
    public function it_does_not_modify_when_class_does_not_implement_for_entity(): void
    {
        $classNode = $this->getClassNode($this->getFixturePath('Entities/ClassWithoutEntityDriven.php'));

        $rule = $this->resolveRule(ForEntityMustHaveAlias::class);
        $result = $rule->refactor($classNode);

        $this->assertNull($result);
    }
}
