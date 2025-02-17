<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use DecodeLabs\Exceptional;

/**
 * @template TValue
 * @phpstan-require-implements MapInterface<int|string,TValue>
 */
trait DictionaryTrait
{
    /**
     * @use CollectionTrait<int|string,TValue,TValue>
     */
    use CollectionTrait;
    use SortableTrait;

    /**
     * Get all keys in array, enforce string formatting
     *
     * @return list<int|string>
     */
    public function getKeys(): array
    {
        return array_keys($this->items);
    }


    /**
     * Retrieve a single entry
     *
     * @param int|string $key
     */
    public function get(
        mixed $key
    ): mixed {
        return $this->items[$key] ?? null;
    }

    /**
     * Retrieve entry and remove from collection
     *
     * @param int|string $key
     */
    public function pull(
        mixed $key
    ): mixed {
        $output = $this->items[$key] ?? null;

        if (static::Mutable) {
            unset($this->items[$key]);
        }

        return $output;
    }

    /**
     * Direct set a value
     *
     * @param int|string $key
     */
    public function set(
        mixed $key,
        mixed $value
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items[$key] = $value;
        return $output;
    }

    /**
     * True if any provided keys have a set value (not null)
     *
     * @param int|string ...$keys
     */
    public function has(
        mixed ...$keys
    ): bool {
        foreach ($keys as $key) {
            if (isset($this->items[$key])) {
                return true;
            }
        }

        return false;
    }

    /**
     * True if all provided keys have a set value (not null)
     *
     * @param int|string ...$keys
     */
    public function hasAll(
        mixed ...$keys
    ): bool {
        foreach ($keys as $key) {
            if (!isset($this->items[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * True if any provided keys are in the collection
     *
     * @param int|string ...$keys
     */
    public function hasKey(
        mixed ...$keys
    ): bool {
        foreach ($keys as $key) {
            if (array_key_exists($key, $this->items)) {
                return true;
            }
        }

        return false;
    }

    /**
     * True if all provided keys are in the collection
     *
     * @param int|string ...$keys
     */
    public function hasKeys(
        mixed ...$keys
    ): bool {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->items)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove all values associated with $keys
     *
     * @param int|string ...$keys
     */
    public function remove(
        mixed ...$keys
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_diff_key($output->items, array_flip($keys));
        return $output;
    }

    /**
     * Remove all values not associated with $keys
     *
     * @param int|string ...$keys
     */
    public function keep(
        mixed ...$keys
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_intersect_key($output->items, array_flip($keys));
        return $output;
    }


    /**
     * Lookup a key by value
     *
     * @return int|string|null
     */
    public function findKey(
        mixed $value,
        bool $strict = false
    ): mixed {
        if (false === ($key = array_search($value, $this->items, $strict))) {
            return null;
        }

        return $key;
    }


    /**
     * Reset all values
     */
    public function clear(): static
    {
        $output = static::Mutable ? $this : clone $this;
        $output->items = [];
        return $output;
    }

    /**
     * Remove all keys
     */
    public function clearKeys(): static
    {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_values($output->items);
        return $output;
    }


    /**
     * Collapse multi dimensional array to flat
     */
    public function collapse(
        bool $unique = false,
        bool $removeNull = false
    ): static {
        $output = static::Mutable ? $this : clone $this;
        // @phpstan-ignore-next-line PHPStan bug
        $output->items = ArrayUtils::collapse($output->items, true, $unique, $removeNull);
        return $output;
    }

    /**
     * Collapse without the keys
     */
    public function collapseValues(
        bool $unique = false,
        bool $removeNull = false
    ): static {
        $output = static::Mutable ? $this : clone $this;
        // @phpstan-ignore-next-line PHPStan bug
        $output->items = ArrayUtils::collapse($output->items, false, $unique, $removeNull);
        return $output;
    }



    /**
     * Switch key case for all entries
     */
    public function changeKeyCase(
        int $case = CASE_LOWER
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_change_key_case($output->items, $case);
        return $output;
    }


    /**
     * Map values of collection to $keys
     *
     * @param iterable<int|string> $keys
     */
    public function combineWithKeys(
        iterable $keys
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $result = array_combine(ArrayUtils::iterableToArray($keys), $output->items);

        // @phpstan-ignore-next-line PHPStan bug
        if($result === false) {
            throw Exceptional::InvalidArgument(
                'Key count does not match value count'
            );
        }

        // @phpstan-ignore-next-line PHPStan bug
        $output->items = $result;
        return $output;
    }

    /**
     * Map $values to values of collection as keys
     *
     * @param iterable<TValue> $values
     */
    public function combineWithValues(
        iterable $values
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $result = array_combine(
            array_filter($output->items, fn($value) => is_string($value) || is_int($value)),
            ArrayUtils::iterableToArray($values)
        );

        // @phpstan-ignore-next-line PHPStan bug
        if($result === false) {
            throw Exceptional::InvalidArgument(
                'Key count does not match value count'
            );
        }

        return $output;
    }


    /**
     * Replace all values with $value
     *
     * @param TValue $value
     */
    public function fill(
        mixed $value
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_fill_keys(array_keys($output->items), $value);
        return $output;
    }

    /**
     * Flip keys and values
     */
    public function flip(): MapInterface
    {
        $output = clone $this;

        /** @var array<TValue> $items */
        $items = array_flip(
            array_filter($output->items, fn($value) => is_string($value) || is_int($value))
        );

        $output->items = $items;

        return $output;
    }


    /**
     * Merge all passed collections into one
     *
     * @param iterable<int|string,TValue> ...$arrays
     */
    public function merge(
        iterable ...$arrays
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_merge($output->items, ...ArrayUtils::iterablesToArrays(...$arrays));
        return $output;
    }

    /**
     * Merge EVERYTHING :D
     *
     * @param iterable<int|string,TValue> ...$arrays
     */
    public function mergeRecursive(
        iterable ...$arrays
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_merge_recursive($output->items, ...ArrayUtils::iterablesToArrays(...$arrays));
        return $output;
    }


    /**
     * Like merge, but replaces.. obvs
     *
     * @param iterable<int|string,TValue> ...$arrays
     */
    public function replace(
        iterable ...$arrays
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_replace($output->items, ...ArrayUtils::iterablesToArrays(...$arrays));
        return $output;
    }

    /**
     * Replace EVERYTHING :D
     *
     * @param iterable<int|string,TValue> ...$arrays
     */
    public function replaceRecursive(
        iterable ...$arrays
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_replace_recursive($output->items, ...ArrayUtils::iterablesToArrays(...$arrays));
        return $output;
    }



    /**
     * Remove $offet + $length items
     *
     * @param-out MapInterface<int|string,TValue> $removed
     */
    public function removeSlice(
        int $offset,
        ?int $length = null,
        ?MapInterface &$removed = null
    ): static {
        $output = static::Mutable ? $this : clone $this;

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
     * @param iterable<int|string,TValue> $replacement
     * @param-out MapInterface<int|string,TValue> $removed
     */
    public function replaceSlice(
        int $offset,
        ?int $length,
        iterable $replacement,
        ?MapInterface &$removed = null
    ): static {
        $output = static::Mutable ? $this : clone $this;

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
    public function unique(
        int $flags = SORT_STRING
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_unique($output->items, $flags);
        return $output;
    }


    /**
     * Iterate each entry
     */
    public function walk(
        callable $callback,
        mixed $data = null
    ): static {
        $output = static::Mutable ? $this : clone $this;
        array_walk($output->items, $callback, $data);
        return $output;
    }

    /**
     * Iterate everything
     */
    public function walkRecursive(
        callable $callback,
        mixed $data = null
    ): static {
        $output = static::Mutable ? $this : clone $this;
        array_walk_recursive($output->items, $callback, $data);
        return $output;
    }




    /**
     * Copy and reinitialise new object
     *
     * @template FValue
     * @param iterable<int|string, FValue> $newItems
     */
    protected static function propagate(
        iterable $newItems = []
    ): static {
        /** @var static $output */
        $output = new self($newItems);

        return $output;
    }
}
