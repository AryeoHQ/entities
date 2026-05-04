<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Support\Entities\Contracts\Entity;

/**
 * @mixin ValidBuilder
 */
#[UseEloquentBuilder(ValidBuilder::class)]
class ModelWithoutHasBuilderAnnotation extends Model implements Entity
{
    use HasBuilder;
}
