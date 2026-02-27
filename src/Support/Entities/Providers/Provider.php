<?php

declare(strict_types=1);

namespace Support\Entities\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Support\Entities\Console\Commands\MakeEntity;
use Support\Entities\Console\Commands\MakePolicy;
use Support\Entities\Console\Commands\MakeProvider;
use Support\Entities\Console\Commands\MakeTestClass;
use Support\Entities\Models\Console\Commands\MakeBuilder;
use Support\Entities\Models\Console\Commands\MakeCollection;
use Support\Entities\Models\Console\Commands\MakeEvent;
use Support\Entities\Models\Console\Commands\MakeFactory;
use Support\Entities\Models\Console\Commands\MakeModel;

class Provider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            MakeBuilder::class,
            MakeCollection::class,
            MakeEvent::class,
            MakeFactory::class,
            MakeEntity::class,
            MakeModel::class,
            MakePolicy::class,
            MakeProvider::class,
            MakeTestClass::class,
        ]);
    }

    public function boot(): void
    {
        Relation::requireMorphMap();
    }
}
