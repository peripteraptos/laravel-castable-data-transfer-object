<?php

namespace JessArcher\CastableDataTransferObject\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use InvalidArgumentException;
use JessArcher\CastableDataTransferObject\CastUsingJsonFlags;
use ReflectionClass;

class DataTransferObject implements CastsAttributes
{
    public function __construct(
        /** @var string The DataTransferObject class to cast to */
        protected string $class,
    ) {
    }

    /**
     * Cast the stored value to the configured DataTransferObject.
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return;
        }

        $object = $this->class::fromJson($value, $this->getJsonFlags()->decode);
        if (method_exists($object, 'tapCast')) {
            $object->tapCast($model, $key);
        }

        return $object;
    }

    protected function getJsonFlags(): CastUsingJsonFlags
    {
        $attributes = (new ReflectionClass($this->class))
            ->getAttributes(CastUsingJsonFlags::class);

        return ($attributes[0] ?? null)?->newInstance()
            ?? new CastUsingJsonFlags();
    }

    /**
     * Prepare the given value for storage.
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return;
        }

        if (is_array($value)) {
            $value = new $this->class($value);
        }

        if (!$value instanceof $this->class) {
            throw new InvalidArgumentException("Value must be of type [$this->class], array, or null");
        }

        return $value->toJson($this->getJsonFlags()->encode);
    }
}
