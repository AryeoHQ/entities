<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Database\Eloquent\Collection;

/** @extends Collection<int, ValidModel> */
final class ValidCollection extends Collection {}
