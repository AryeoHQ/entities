<?php

declare(strict_types=1);

namespace Tests\Fixtures\Support\Posts;

use Illuminate\Database\Eloquent\Factories\Factory as EloquentFactory;

/**
 * @extends EloquentFactory<Post>
 */
final class Factory extends EloquentFactory
{
    protected $model = Post::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [];
    }
}
