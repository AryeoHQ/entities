<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Support\Entities\Contracts\Entity;

#[UseFactory(ValidFactory::class)]
class ModelWithoutHasFactoryGeneric extends Model implements Entity
{
    use HasFactory;
}
