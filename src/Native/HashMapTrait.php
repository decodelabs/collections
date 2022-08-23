<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Native;

use DecodeLabs\Collections\ArrayUtils;
use DecodeLabs\Collections\Collection;
use DecodeLabs\Collections\HashMap;

/**
 * @template TValue
 */
trait HashMapTrait
{
    /**
     * @use CollectionTrait<int|string, TValue>
     */
    use CollectionTrait;
    use SortableTrait;

    /**
     * Get all keys in array, enforce string formatting
     */
    public function getKeys(): array
    {
        return array_map('strval', array_keys($this->items));
    }


    /**
     * Retrieve a single entry
     */
    public function get(int|string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    /**
     * Retrieve entry and remove from collection
     */
    public function pull(int|string $key): mixed
    {
        $output = $this->items[$key] ?? null;

        if (static::MUTABLE) {
            unset($this->items[$key]);
        }

        return $output;
    }

    /**
     * Direct set a value
     */
    public function set(
        int|string $key,
        mixed $value
    ): HashMap {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items[$key] = $value;
        return $output;
    }

    /**
     * True if any provided keys have a set value (not null)
     */
    public function has(int|string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (isset($this->items[$key])) {
                return true;
            }
        }

        return false;
    }

    /**
     * True if all provided keys have a set value (not null)
     */
    public function hasAll(int|string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (!isset($this->items[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * True if any provided keys are in the collection
     */
    public function hasKey(int|string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $this->items)) {
                return true;
            }
        }

        return false;
    }

    /**
     * True if all provided keys are in the collection
     */
    public function hasKeys(int|string ...$keys): bool
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->items)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove all values associated with $keys
     */
    public function remove(int|string ...$keys): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_diff_key($output->items, array_flip($keys));
        return $output;
    }

    /**
     * Remove all values not associated with $keys
     */
    public function keep(int|string ...$keys): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_intersect_key($output->items, array_flip($keys));
        return $output;
    }


    /**
     * Lookup a key by value
     */
    public function findKey(
        mixed $value,
        bool $strict = false
    ): int|string|null {
        if (false === ($key = array_search($value, $this->items, $strict))) {
            return null;
        }

        return $key;
    }


    /**
     * Reset all values
     */
    public function clear(): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = [];
        return $output;
    }

    /**
     * Remove all keys
     */
    public function clearKeys(): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_values($output->items);
        return $output;
    }


    /**
     * Collapse multi dimensional array to flat
     */
    public function collapse(
        bool $unique = false,
        bool $removeNull = false
    ): HashMap {
        $output = static::MUTABLE ? $this : clone $this;
        /* @phpstan-ignore-next-line */
        $output->items = ArrayUtils::collapse($output->items, true, $unique, $removeNull);
        return $output;
    }

    /**
     * Collapse without the keys
     */
    public function collapseValues(
        bool $unique = false,
        bool $removeNull = false
    ): HashMap {
        $output = static::MUTABLE ? $this : clone $this;
        /* @phpstan-ignore-next-line */
        $output->items = ArrayUtils::collapse($output->items, false, $unique, $removeNull);
        return $output;
    }



    /**
     * Switch key case for all entries
     */
    public function changeKeyCase(int $case = CASE_LOWER): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_change_key_case($output->items, $case);
        return $output;
    }


    /**
     * Map values of collection to $keys
     */
    public function combineWithKeys(iterable $keys): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;

        if (false !== ($result = array_combine(ArrayUtils::iterableToArray($keys), $output->items))) {
            $output->items = $result;
        }

        return $output;
    }

    /**
     * Map $values to values of collection as keys
     */
    public function combineWithValues(iterable $values): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;

        /* @phpstan-ignore-next-line */
        if (false !== ($result = array_combine($output->items, ArrayUtils::iterableToArray($values)))) {
            $output->items = $result;
        }

        return $output;
    }


    /**
     * Replace all values with $value
     */
    public function fill(mixed $value): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_fill_keys(array_keys($output->items), $value);
        return $output;
    }

    /**
     * Flip keys and values
     */
    public function flip(): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;

        /** @phpstan-var array<TValue> $items */
        $items = array_flip($output->items); /* @phpstan-ignore-line */
        $output->items = $items;

        return $output;
    }


    /**
     * Merge all passed collections into one
     */
    public function merge(iterable ...$arrays): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_merge($output->items, ...ArrayUtils::iterablesToArrays(...$arrays));
        return $output;
    }

    /**
     * Merge EVERYTHING :D
     */
    public function mergeRecursive(iterable ...$arrays): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_merge_recursive($output->items, ...ArrayUtils::iterablesToArrays(...$arrays));
        return $output;
    }


    /**
     * Like merge, but replaces.. obvs
     */
    public function replace(iterable ...$arrays): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_replace($output->items, ...ArrayUtils::iterablesToArrays(...$arrays));
        return $output;
    }

    /**
     * Replace EVERYTHING :D
     */
    public function replaceRecursive(iterable ...$arrays): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_replace_recursive($output->items, ...ArrayUtils::iterablesToArrays(...$arrays));
        return $output;
    }



    /**
     * Remove $offet + $length items
     *
     * @phpstan-param static<TValue>|null $removed
     * @phpstan-return static<TValue>
     */
    public function removeSlice(
        int $offset,
        int $length = null,
        HashMap &$removed = null
    ): HashMap {
        $output = static::MUTABLE ? $this : clone $this;

        if ($length === null) {
            $length = count($output->items);
        }

        $removed = $this->propagate(
            array_splice($output->items, $offset, $length)
        );

        return $output;
    }

    /**
     * Like removeSlice, but leaves a present behind
     *
     * @phpstan-param iterable<TValue> $replacement
     * @phpstan-param static<TValue>|null $removed
     * @phpstan-return static<TValue>
     */
    public function replaceSlice(
        int $offset,
        int $length = null,
        iterable $replacement,
        HashMap &$removed = null
    ): HashMap {
        $output = static::MUTABLE ? $this : clone $this;

        if ($length === null) {
            $length = count($output->items);
        }

        $removed = $this->propagate(
            array_splice($output->items, $offset, $length, ArrayUtils::iterableToArray($replacement))
        );

        return $output;
    }


    /**
     * Remove duplicates from collection
     */
    public function unique(int $flags = SORT_STRING): HashMap
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_unique($output->items, $flags);
        return $output;
    }


    /**
     * Iterate each entry
     */
    public function walk(
        callable $callback,
        mixed $data = null
    ): HashMap {
        $output = static::MUTABLE ? $this : clone $this;
        array_walk($output->items, $callback, $data);
        return $output;
    }

    /**
     * Iterate everything
     */
    public function walkRecursive(
        callable $callback,
        mixed $data = null
    ): HashMap {
        $output = static::MUTABLE ? $this : clone $this;
        array_walk_recursive($output->items, $callback, $data);
        return $output;
    }




    /**
     * Copy and reinitialise new object
     *
     * @template FValue
     * @param iterable<int|string, FValue> $newItems
     * @return static<FValue>
     */
    protected static function propagate(iterable $newItems = []): self
    {
        return new self($newItems);
    }
}
