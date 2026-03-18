<?php

declare(strict_types=1);

namespace Support\Entities\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Support\Entities\Console\Commands\MakeEntity;
use Support\Entities\Console\Commands\MakePolicy;
use Support\Entities\Console\Commands\MakeProvider;
use Support\Entities\Models\Console\Commands\MakeBuilder;
use Support\Entities\Models\Console\Commands\MakeCollection;
use Support\Entities\Models\Console\Commands\MakeEvent;
use Support\Entities\Models\Console\Commands\MakeFactory;
use Support\Entities\Models\Console\Commands\MakeModel;
use Tests\TestCase;

#[CoversClass(Provider::class)]
class ProviderTest extends TestCase
{
    /**
     * @return array<string, array{class-string}>
     */
    public static function registeredCommands(): array
    {
        return [
            'MakeBuilder' => [MakeBuilder::class],
            'MakeCollection' => [MakeCollection::class],
            'MakeEntity' => [MakeEntity::class],
            'MakeEvent' => [MakeEvent::class],
            'MakeFactory' => [MakeFactory::class],
            'MakeModel' => [MakeModel::class],
            'MakePolicy' => [MakePolicy::class],
            'MakeProvider' => [MakeProvider::class],
        ];
    }

    #[DataProvider('registeredCommands')]
    #[Test]
    public function it_registers_all_commands(string $command): void
    {
        $this->artisan($command, ['--help' => true])
            ->assertSuccessful();
    }

    #[Test]
    public function it_overrides_make_model(): void
    {
        $resolved = $this->app->make(MakeModel::class);

        $this->assertSame('make:model', $resolved->getName());
    }

    #[Test]
    public function it_overrides_make_policy(): void
    {
        $resolved = $this->app->make(MakePolicy::class);

        $this->assertSame('make:policy', $resolved->getName());
    }

    #[Test]
    public function it_overrides_make_provider(): void
    {
        $resolved = $this->app->make(MakeProvider::class);

        $this->assertSame('make:provider', $resolved->getName());
    }

    #[Test]
    public function it_requires_morph_map_by_default(): void
    {
        $this->assertTrue(Relation::requiresMorphMap());
    }

    #[Test]
    public function it_does_not_require_morph_map_when_config_is_false(): void
    {
        config()->set('entities.require_morph_map', false);

        (new Provider($this->app))->boot();

        $this->assertFalse(Relation::requiresMorphMap());
    }
}
