<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Database\Eloquent\Model;
use Support\Entities\Contracts\Entity;

class ModelWithoutHasFactory extends Model implements Entity {}
