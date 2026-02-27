<?php

declare(strict_types=1);

namespace Tests\Fixtures\Tooling\Entities;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Support\Entities\Contracts\Entity;

class ModelWithScopeMethod extends Model implements Entity
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    #[Scope]
    public function published(Builder $query): Builder
    {
        return $query->where('published', true);
    }
}
