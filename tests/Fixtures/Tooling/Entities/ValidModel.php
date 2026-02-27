<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Support\Entities\Contracts\Entity;

#[CollectedBy(ValidCollection::class)]
#[UseEloquentBuilder(ValidBuilder::class)]
#[UseFactory(ValidFactory::class)]
#[UsePolicy(ValidPolicy::class)]
class ValidModel extends Model implements Entity
{
    /** @use HasFactory<ValidFactory> */
    use HasFactory;

    use HasUuids;
}
