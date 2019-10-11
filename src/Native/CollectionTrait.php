<?php
/**
 * This file is part of the Collections package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Collections\Native;

use DecodeLabs\Collections\ArrayUtils;
use DecodeLabs\Collections\Collection;

use DecodeLabs\Gadgets\ThenTrait;

trait CollectionTrait
{
    use ThenTrait;

    //const MUTABLE = false;

    protected $items = [];

    /**
     * Direct set items
     */
    public function __construct(iterable $items)
    {
        $this->items = ArrayUtils::iterableToArray($items);
    }


    /**
     * Can the values in this collection change?
     */
    public function isMutable(): bool
    {
        return static::MUTABLE;
    }

    /**
     * Is array empty?
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }


    /**
     * Duplicate collection, can change type if needed
     */
    public function copy(): Collection
    {
        return clone $this;
    }


    /**
     * Iterator interface
     */
    public function getIterator(): \Iterator
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Convert to json
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }



    /**
     * Get dump info
     */
    public function __debugInfo(): array
    {
        return $this->items;
    }
}
