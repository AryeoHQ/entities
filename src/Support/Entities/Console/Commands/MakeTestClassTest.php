<?php

declare(strict_types=1);

namespace Support\Entities\Console\Commands;

use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\References\Entity;
use Tests\TestCase;

#[CoversClass(MakeTestClass::class)]
class MakeTestClassTest extends TestCase
{
    use InteractsWithPublishedFiles; // @phpstan-ignore-line

    public Entity $entity {
        get => new Entity('Foo', 'App\\');
    }

    /** @var array<array-key, string> */
    protected array $files {
        get => [
            $this->entity->directory->append('/*')->toString(),
        ];
    }

    #[Test]
    public function it_generates_a_co_located_test(): void
    {
        $this->artisan(MakeTestClass::class, ['class' => $this->entity->fqcn->toString()])
            ->assertSuccessful();

        $this->assertFileExists($this->entity->test->filePath->toString());
    }

    #[Test]
    public function the_generated_test_has_the_correct_namespace(): void
    {
        $this->artisan(MakeTestClass::class, ['class' => $this->entity->fqcn->toString()])
            ->assertSuccessful();

        $contents = file_get_contents($this->entity->test->filePath->toString());

        $this->assertStringContainsString(
            'namespace '.$this->entity->namespace.';',
            $contents,
        );
    }

    #[Test]
    public function the_generated_test_imports_the_class_under_test(): void
    {
        $this->artisan(MakeTestClass::class, ['class' => $this->entity->fqcn->toString()])
            ->assertSuccessful();

        $contents = file_get_contents($this->entity->test->filePath->toString());

        $this->assertStringContainsString(
            'use '.$this->entity->fqcn.';',
            $contents,
        );
    }

    #[Test]
    public function the_generated_test_has_the_covers_class_attribute(): void
    {
        $this->artisan(MakeTestClass::class, ['class' => $this->entity->fqcn->toString()])
            ->assertSuccessful();

        $contents = file_get_contents($this->entity->test->filePath->toString());

        $this->assertStringContainsString(
            '#[CoversClass('.$this->entity->name.'::class)]',
            $contents,
        );
    }

    #[Test]
    public function the_generated_test_extends_test_case(): void
    {
        $this->artisan(MakeTestClass::class, ['class' => $this->entity->fqcn->toString()])
            ->assertSuccessful();

        $contents = file_get_contents($this->entity->test->filePath->toString());

        $this->assertStringContainsString(
            'final class '.$this->entity->name.'Test extends TestCase',
            $contents,
        );
    }
}
