<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Database\Eloquent\Attributes\CollectedBy;
use Illuminate\Database\Eloquent\Model;
use Support\Entities\Contracts\Entity;

#[CollectedBy(ValidCollection::class)]
class ModelWithoutUsePolicy extends Model implements Entity {}
