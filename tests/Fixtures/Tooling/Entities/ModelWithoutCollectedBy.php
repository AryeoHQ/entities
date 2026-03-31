<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Support\Entities\Contracts\Entity;

#[UseFactory(ValidFactory::class)]
class ModelWithoutCollectedBy extends Model implements Entity
{
    use HasUuids;
}
