<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Database\Eloquent\Builder;

/** @extends Builder<ValidModel> */
final class ValidBuilder extends Builder {}
