<?php

declare(strict_types=1);

namespace Tests\Fixtures\Support\Posts;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Support\Entities\Contracts\Entity;
use Tests\Fixtures\Support\Posts\Events\Created;

#[UseFactory(Factory::class)]
class Post extends Model implements Entity // @phpstan-ignore entities.Model.CollectedBy.required, entities.Model.UseEloquentBuilder.required, entities.Model.UsePolicy.required, entities.Model.HasUuids.required
{
    /** @use HasFactory<Factory> */
    use HasFactory;

    /** @var array<string, class-string> */
    protected $dispatchesEvents = [
        'created' => Created::class,
    ];
}
