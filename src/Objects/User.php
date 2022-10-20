<?php

namespace MOIREI\EventTracking\Objects;

use ArrayObject;
use Illuminate\Contracts\Support\Arrayable;

class User extends ArrayObject implements Arrayable
{
    public string|int $id;

    public ?string $name = null;

    public ?string $firstName = null;

    public ?string $lastName = null;

    public ?string $email = null;

    public ?string $createdAt = null;

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    /**
     * Fill object data
     *
     * @param array $attributes
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            } else {
                $this[$key] = $value;
            }
        }
    }

    /**
     * Get the instance as an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray()
    {
        return array_merge(get_object_vars($this), $this->getArrayCopy());
    }
}
