<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Native;

use ArrayIterator;
use Closure;

use DecodeLabs\Collections\ArrayUtils;
use DecodeLabs\Collections\Collection;
use DecodeLabs\Fluidity\ThenTrait;

use Iterator;
use ReflectionFunction;

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
    final public function copy(): Collection
    {
        return clone $this;
    }



    /**
     * Count items
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Get first item, matching filter
     */
    public function getFirst(callable $filter = null)
    {
        return ArrayUtils::getFirst($this->items, $filter, $this);
    }

    /**
     * Get the last item in the list, matching filter
     */
    public function getLast(callable $filter = null)
    {
        return ArrayUtils::getLast($this->items, $filter, $this);
    }

    /**
     * Pick one entry at random
     */
    public function getRandom()
    {
        return ArrayUtils::getRandom($this->items);
    }


    /**
     * Get all keys in array
     */
    public function getKeys(): Collection
    {
        return $this->propagate(array_keys($this->items));
    }


    /**
     * Is the value in the collection?
     */
    public function contains($value, bool $strict = false): bool
    {
        return in_array($value, $this->items, $strict);
    }

    /**
     * Is the value in the collection, including child arrays?
     */
    public function containsRecursive($value, bool $strict = false): bool
    {
        return ArrayUtils::inArrayRecursive($value, $this->items, $strict);
    }



    /**
     * Return new collection containing $offset + $length items
     */
    public function slice(int $offset, int $length = null): Collection
    {
        return $this->propagate(array_slice(
            $this->items,
            $offset,
            $length,
            true
        ));
    }

    /**
     * Pick a random $number length set of items
     */
    public function sliceRandom(int $number): Collection
    {
        return $this->propagate(ArrayUtils::sliceRandom($this->items, $number));
    }


    /**
     * Split the current items into $size length chunks, maintain keys
     */
    public function chunk(int $size): Collection
    {
        return $this->propagate(array_chunk($this->items, $size, true));
    }

    /**
     * Split the current items into $size length chunks, ignore keys
     */
    public function chunkValues(int $size): Collection
    {
        return $this->propagate(array_chunk($this->items, $size, false));
    }


    /**
     * Return indexed sum list - filters non scalar first
     */
    public function countValues(): Collection
    {
        return $this->propagate(array_count_values(
            array_filter($this->items, function ($value) {
                return is_string($value) || is_int($value);
            })
        ));
    }


    /**
     * Return all items in collection where value or key not in $arrays
     */
    public function diffAssoc(iterable ...$arrays): Collection
    {
        return $this->propagate(array_diff_assoc(
            $this->items,
            ...ArrayUtils::iterablesToArrays(...$arrays)
        ));
    }

    /**
     * DiffAssoc with custom key comparator
     */
    public function diffAssocBy(callable $keyCallback, iterable ...$arrays): Collection
    {
        $args = ArrayUtils::iterablesToArrays(...$arrays);
        $args[] = $keyCallback;

        return $this->propagate(array_diff_uassoc(
            $this->items,
            ...$args
        ));
    }

    /**
     * DiffAssoc with custom value comparator
     */
    public function diffAssocByValue(callable $valueCallback, iterable ...$arrays): Collection
    {
        $args = ArrayUtils::iterablesToArrays(...$arrays);
        $args[] = $valueCallback;

        return $this->propagate(array_udiff_assoc(
            $this->items,
            ...$args
        ));
    }

    /**
     * DiffAssoc with custom key and value comparator
     */
    public function diffAssocAll(callable $valueCallback, callable $keyCallback, iterable ...$arrays): Collection
    {
        $args = ArrayUtils::iterablesToArrays(...$arrays);
        $args[] = $valueCallback;
        $args[] = $keyCallback;

        return $this->propagate(array_udiff_uassoc(
            $this->items,
            ...$args
        ));
    }

    /**
     * Return all items in collection where value not in $arrays
     */
    public function diffValues(iterable ...$arrays): Collection
    {
        return $this->propagate(array_diff(
            $this->items,
            ...ArrayUtils::iterablesToArrays(...$arrays)
        ));
    }

    /**
     * DiffValues with custom value comparator
     */
    public function diffValuesBy(callable $valueCallback, iterable ...$arrays): Collection
    {
        $args = ArrayUtils::iterablesToArrays(...$arrays);
        $args[] = $valueCallback;

        return $this->propagate(array_udiff(
            $this->items,
            ...$args
        ));
    }

    /**
     * Return all items in collection where key not in $arrays
     */
    public function diffKeys(iterable ...$arrays): Collection
    {
        return $this->propagate(array_diff_key(
            $this->items,
            ...ArrayUtils::iterablesToArrays(...$arrays)
        ));
    }

    /**
     * DiffKeys with custom key comparator
     */
    public function diffKeysBy(callable $keyCallback, iterable ...$arrays): Collection
    {
        $args = ArrayUtils::iterablesToArrays(...$arrays);
        $args[] = $keyCallback;

        return $this->propagate(array_diff_ukey(
            $this->items,
            ...$args
        ));
    }


    /**
     * Return all items in collection where value or key in $arrays
     */
    public function intersectAssoc(iterable ...$arrays): Collection
    {
        return $this->propagate(array_intersect_assoc(
            $this->items,
            ...ArrayUtils::iterablesToArrays(...$arrays)
        ));
    }

    /**
     * IntersectAssoc with custom key comparator
     */
    public function intersectAssocBy(callable $keyCallback, iterable ...$arrays): Collection
    {
        $args = ArrayUtils::iterablesToArrays(...$arrays);
        $args[] = $keyCallback;

        return $this->propagate(array_intersect_uassoc(
            $this->items,
            ...$args
        ));
    }

    /**
     * IntersectAssoc with custom value comparator
     */
    public function intersectAssocByValue(callable $valueCallback, iterable ...$arrays): Collection
    {
        $args = ArrayUtils::iterablesToArrays(...$arrays);
        $args[] = $valueCallback;

        return $this->propagate(array_uintersect_assoc(
            $this->items,
            ...$args
        ));
    }

    /**
     * IntersectAssoc with custom key and value comparator
     */
    public function intersectAssocAll(callable $valueCallback, callable $keyCallback, iterable ...$arrays): Collection
    {
        $args = ArrayUtils::iterablesToArrays(...$arrays);
        $args[] = $valueCallback;
        $args[] = $keyCallback;

        return $this->propagate(array_uintersect_uassoc(
            $this->items,
            ...$args
        ));
    }

    /**
     * Return all items in collection where value in $arrays
     */
    public function intersectValues(iterable ...$arrays): Collection
    {
        return $this->propagate(array_intersect(
            $this->items,
            ...ArrayUtils::iterablesToArrays(...$arrays)
        ));
    }

    /**
     * IntersectValues with custom value comparator
     */
    public function intersectValuesBy(callable $valueCallback, iterable ...$arrays): Collection
    {
        $args = ArrayUtils::iterablesToArrays(...$arrays);
        $args[] = $valueCallback;

        return $this->propagate(array_uintersect(
            $this->items,
            ...$args
        ));
    }

    /**
     * Return all items in collection where key in $arrays
     */
    public function intersectKeys(iterable ...$arrays): Collection
    {
        return $this->propagate(array_intersect_key(
            $this->items,
            ...ArrayUtils::iterablesToArrays(...$arrays)
        ));
    }

    /**
     * IntersectKeys with custom key comparator
     */
    public function intersectKeysBy(callable $keyCallback, iterable ...$arrays): Collection
    {
        $args = ArrayUtils::iterablesToArrays(...$arrays);
        $args[] = $keyCallback;

        return $this->propagate(array_intersect_ukey(
            $this->items,
            ...$args
        ));
    }


    /**
     * Return subset of collection where callback returns true
     */
    public function filter(callable $callback = null): Collection
    {
        if ($callback) {
            return $this->propagate(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        } else {
            return $this->propagate(array_filter($this->items));
        }
    }

    /**
     * Combine collection with passed arrays via callback
     */
    public function map(callable $callback, iterable ...$arrays): Collection
    {
        return $this->propagate(array_map(
            $callback,
            $this->items,
            ...ArrayUtils::iterablesToArrays(...$arrays)
        ));
    }

    /**
     * Loop through collection to build new collection
     */
    public function mapSelf(callable $callback): Collection
    {
        if (
            $callback instanceof Closure &&
            (new ReflectionFunction($callback))->isGenerator()
        ) {
            $output = [];

            foreach ($this->items as $key => $value) {
                $output = array_merge($output, iterator_to_array($callback($value, $key)));
            }

            return $this->propagate($output);
        } else {
            $keys = array_keys($this->items);
            $items = array_map($callback, $this->items, $keys);
            return $this->propagate(array_combine($keys, $items));
        }
    }

    /**
     * Whittle down collection to single value
     */
    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial);
    }


    /**
     * Add all numeric values in collection
     */
    public function getSum(callable $filter = null)
    {
        return $this->reduce(function ($result, $item) use ($filter) {
            if ($filter) {
                $item = $filter($item);
            }

            if (!is_numeric($item)) {
                return $result;
            }

            return $result + $item;
        }, 1);
    }

    /**
     * Multiple all numeric values in collection
     */
    public function getProduct(callable $filter = null)
    {
        return $this->reduce(function ($result, $item) use ($filter) {
            if ($filter) {
                $item = $filter($item);
            }

            if (!is_numeric($item)) {
                return $result;
            }

            return $result * $item;
        }, 1);
    }

    /**
     * Get average value of numerics
     */
    public function getAvg(callable $filter = null)
    {
        if (!$count = count($this->items)) {
            return null;
        }

        return $this->getSum($filter) / $count;
    }


    /**
     * Combine a column with optional key column into single array
     */
    public function pluck(string $valueKey, string $indexKey = null): Collection
    {
        return $this->propagate(array_column($this->items, $valueKey, $indexKey));
    }





    /**
     * Set by array access
     */
    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Get by array access
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Check by array access
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Remove by array access
     */
    public function offsetUnset($key): void
    {
        $this->remove($key);
    }




    /**
     * Iterator interface
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->items);
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


    /**
     * Copy and reinitialise new object
     */
    abstract protected static function propagate(iterable $newItems = []): Collection;
}
