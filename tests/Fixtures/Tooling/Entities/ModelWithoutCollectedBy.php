<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Support\Entities\Contracts\Entity;

class ModelWithoutCollectedBy extends Model implements Entity
{
    use HasUuids;
}
