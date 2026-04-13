<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ValidModel> */
final class ValidFactory extends Factory
{
    /** @var class-string<ValidModel> */
    protected $model = ValidModel::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [];
    }
}
