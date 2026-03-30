<?php

declare(strict_types=1);

namespace Support\Entities\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Support\Entities\Console\Commands\MakeEntity;
use Support\Entities\Console\Commands\MakePolicy;
use Support\Entities\Console\Commands\MakeProvider;
use Support\Entities\Models\Console\Commands\MakeBuilder;
use Support\Entities\Models\Console\Commands\MakeCollection;
use Support\Entities\Models\Console\Commands\MakeEvent;
use Support\Entities\Models\Console\Commands\MakeFactory;
use Support\Entities\Models\Console\Commands\MakeModel;
use Tooling\Entities\Composer\ClassMap\Collectors\Entities;
use Tooling\Entities\Composer\ClassMap\Collectors\Models;

class Provider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../../../config/entities.php', 'entities');

        $this->app->tag([Entities::class, Models::class], 'tooling.classmap.collectors');

        $this->commands([
            MakeBuilder::class,
            MakeCollection::class,
            MakeEvent::class,
            MakeFactory::class,
            MakeEntity::class,
            MakeModel::class,
            MakePolicy::class,
            MakeProvider::class,
        ]);
    }

    public function boot(): void
    {
        Relation::requireMorphMap(config('entities.require_morph_map'));
    }
}
