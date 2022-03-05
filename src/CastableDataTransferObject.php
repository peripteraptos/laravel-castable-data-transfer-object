<?php

namespace JessArcher\CastableDataTransferObject;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use JessArcher\CastableDataTransferObject\Casts\DataTransferObject as DataTransferObjectCast;
use function Safe\json_decode;
use function Safe\json_encode;
use Spatie\DataTransferObject\DataTransferObject;

abstract class CastableDataTransferObject extends DataTransferObject implements Castable, Arrayable, Jsonable
{
    protected ?Model $parent;
    protected ?string $parent_key;

    public static function castUsing(array $arguments)
    {
        return new DataTransferObjectCast(static::class);
    }

    public static function fromJson(string $json, int $options = 0)
    {
        return new static(json_decode($json, assoc: true, options: $options));
    }

    public function attachParent(Model $model, string $key)
    {
        $this->parent = $model;
        $this->parent_key = $key;
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
