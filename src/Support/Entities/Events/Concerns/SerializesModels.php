<?php

declare(strict_types=1);

namespace Support\Entities\Events\Concerns;

use Illuminate\Support\Arr;
use Support\Entities\Events\Dispatcher\Mixins\DisablesSerializesModels;

trait SerializesModels
{
    use \Illuminate\Queue\SerializesModels {
        getSerializedPropertyValue as private serializesModelsSerializedPropertyValue;
    }

    private bool $disableSerializesModel = false;

    /**
     * @param  mixed  $value
     * @param  bool  $withRelations
     * @return mixed
     */
    protected function getSerializedPropertyValue($value, $withRelations = true)
    {
        if ($this->disableSerializesModel) {
            return $value;
        }

        $disabled = DisablesSerializesModels::$events;

        when(
            $disabled === true || (is_array($disabled) && Arr::first($disabled, fn (string $class) => $this instanceof $class)),
            fn () => $this->disableSerializesModel = true
        );

        return match ($this->disableSerializesModel) {
            true => $value,
            false => $this->serializesModelsSerializedPropertyValue($value, $withRelations),
        };
    }
}
