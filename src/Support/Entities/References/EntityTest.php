<?php

declare(strict_types=1);

namespace Support\Entities\References;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\References\Concerns\EntityTestCases;
use Support\Entities\References\Contracts\Entity as EntityContract;
use Tests\Support\Entities\References\Contracts\TestsEntity;
use Tests\TestCase;

#[CoversClass(Entity::class)]
class EntityTest extends TestCase implements TestsEntity
{
    use EntityTestCases;

    public EntityContract $subject {
        get => new Entity(name: 'Post', baseNamespace: 'App\\');
    }

    #[Test]
    public function it_stores_name_and_base_namespace(): void
    {
        $this->assertSame('Post', $this->subject->name->toString());
        $this->assertSame('App\\', $this->subject->baseNamespace->toString());
    }

    #[Test]
    public function it_can_be_created_from_fqcn(): void
    {
        $entity = Entity::fromFqcn('App\\Entities\\Posts\\Post');

        $this->assertSame('Post', $entity->name->toString());
        $this->assertSame('App\\', $entity->baseNamespace->toString());
    }
}
