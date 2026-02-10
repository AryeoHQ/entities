<?php

declare(strict_types=1);

namespace Tests\Tooling\Entities\Rector;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\Class_;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tooling\Entities\Rector\ModelMustImplementEntity;

#[CoversClass(ModelMustImplementEntity::class)]
class ModelMustImplementEntityTest extends TestCase
{
    #[Test]
    public function it_adds_entity_interface_to_model_without_it(): void
    {
        $rector = $this->app->make(ModelMustImplementEntity::class);

        $node = new Class_('TestModel');
        $node->extends = new FullyQualified(Model::class);
        $node->implements = [];

        $result = $rector->refactor($node);

        $this->assertInstanceOf(Class_::class, $result);
        $this->assertCount(1, $result->implements);
        $this->assertInstanceOf(Name::class, $result->implements[0]);
        $this->assertEquals('Entity', $result->implements[0]->toString());
    }

    #[Test]
    public function it_does_not_modify_model_that_already_implements_entity(): void
    {
        $rector = $this->app->make(ModelMustImplementEntity::class);

        $node = new Class_('TestModel');
        $node->extends = new FullyQualified(Model::class);
        $node->implements = [new Name('Entity')];

        $result = $rector->refactor($node);

        $this->assertNull($result);
    }

    #[Test]
    public function it_does_not_modify_non_model_classes(): void
    {
        $rector = $this->app->make(ModelMustImplementEntity::class);

        $node = new Class_('TestClass');
        $node->extends = null;
        $node->implements = [];

        $result = $rector->refactor($node);

        $this->assertNull($result);
    }

    #[Test]
    public function it_adds_entity_to_existing_implements(): void
    {
        $rector = $this->app->make(ModelMustImplementEntity::class);

        $node = new Class_('TestModel');
        $node->extends = new FullyQualified(Model::class);
        $node->implements = [new Name('SomeInterface')];

        $result = $rector->refactor($node);

        $this->assertInstanceOf(Class_::class, $result);
        $this->assertCount(2, $result->implements);
        $this->assertEquals('SomeInterface', $result->implements[0]->toString());
        $this->assertEquals('Entity', $result->implements[1]->toString());
    }
}
