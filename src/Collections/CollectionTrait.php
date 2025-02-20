<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use ArrayIterator;
use Closure;
use DecodeLabs\Coercion;
use DecodeLabs\Collections\ArrayUtils;
use DecodeLabs\Collections\Collection;
use DecodeLabs\Collections\Dictionary;
use DecodeLabs\Collections\SequenceInterface;
use DecodeLabs\Exceptional;
use DecodeLabs\Fluidity\ThenTrait;
use Generator;
use Iterator;
use ReflectionFunction;

/**
 * @template TKey
 * @template TValue
 * @template TIterate = TValue
 * @phpstan-require-implements Collection<TKey,TValue,TIterate>
 */
trait CollectionTrait
{
    use ThenTrait;

    //protected const bool Mutable = false;

    /**
     * @var array<TKey,TValue>
     */
    protected array $items = [];

    /**
     * Direct set items
     *
     * @param iterable<TKey, TValue> $items
     */
    public function __construct(
        iterable $items
    ) {
        $this->items = ArrayUtils::iterableToArray($items);
    }


    /**
     * Can the values in this collection change?
     */
    public function isMutable(): bool
    {
        return static::Mutable;
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
    final public function copy(): static
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
    public function getFirst(
        ?callable $filter = null
    ): mixed {
        return ArrayUtils::getFirst($this->items, $filter, $this);
    }

    /**
     * Get the last item in the list, matching filter
     */
    public function getLast(
        ?callable $filter = null
    ): mixed {
        return ArrayUtils::getLast($this->items, $filter, $this);
    }

    /**
     * Pick one entry at random
     */
    public function getRandom(): mixed
    {
        return ArrayUtils::getRandom($this->items);
    }



    /**
     * Add items to the end
     */
    public function push(
        mixed ...$values
    ): static {
        return $this->append(...$values);
    }

    /**
     * Pull first item
     */
    public function pop(): mixed
    {
        if (static::Mutable) {
            return array_pop($this->items);
        } else {
            return $this->getLast();
        }
    }

    /**
     * Add items to the start
     */
    public function unshift(
        mixed ...$values
    ): static {
        return $this->prepend(...$values);
    }

    /**
     * Pull last item
     */
    public function shift(): mixed
    {
        if (static::Mutable) {
            return array_shift($this->items);
        } else {
            return $this->getFirst();
        }
    }


    /**
     * Add items to the end
     */
    public function append(
        mixed ...$values
    ): static {
        $output = static::Mutable ? $this : clone $this;
        array_push($output->items, ...$values);
        return $output;
    }

    /**
     * Add items to the start
     */
    public function prepend(
        mixed ...$values
    ): static {
        $output = static::Mutable ? $this : clone $this;
        array_unshift($output->items, ...$values);
        return $output;
    }



    /**
     * Get all keys in array
     */
    public function getKeys(): array
    {
        return array_keys($this->items);
    }


    /**
     * Is the value in the collection?
     */
    public function contains(
        mixed $value,
        bool $strict = false
    ): bool {
        return in_array($value, $this->items, $strict);
    }

    /**
     * Is the value in the collection, including child arrays?
     */
    public function containsRecursive(
        mixed $value,
        bool $strict = false
    ): bool {
        return ArrayUtils::inArrayRecursive($value, $this->items, $strict);
    }



    /**
     * Return new collection containing $offset + $length items
     */
    public function slice(
        int $offset,
        ?int $length = null
    ): static {
        $output = array_slice(
            array: $this->items,
            offset: $offset,
            length: $length,
            preserve_keys: true
        );

        return $this->propagate($output);
    }

    /**
     * Pick a random $number length set of items
     */
    public function sliceRandom(
        int $number
    ): static {
        $output = ArrayUtils::sliceRandom(
            array: $this->items,
            number: $number
        );

        /**
         * Ignore required for Tree
         * @phpstan-ignore-next-line
         */
        return $this->propagate($output);
    }


    /**
     * Split the current items into $size length chunks, maintain keys
     *
     * @param int<1,max> $size
     */
    public function chunk(
        int $size
    ): array {
        $output = [];

        foreach (array_chunk(
            array: $this->items,
            length: $size,
            preserve_keys: true
        ) as $chunk) {
            $output[] = $this->propagate($chunk);
        }

        return $output;
    }

    /**
     * Split the current items into $size length chunks, ignore keys
     *
     * @param int<1,max> $size
     */
    public function chunkValues(
        int $size
    ): array {
        $output = [];

        foreach (array_chunk(
            array: $this->items,
            length: $size,
            preserve_keys: false
        ) as $chunk) {
            $output[] = $this->propagate($chunk);
        }

        return $output;
    }


    /**
     * Return indexed sum list - filters non scalar first
     *
     * @return MapInterface<TValue,int>
     */
    public function countValues(): MapInterface
    {
        $output = array_count_values(
            array_filter($this->items, function ($value) {
                return
                    is_string($value) ||
                    is_int($value);
            })
        );

        /** @var MapInterface<TValue,int> */
        $output = new Dictionary($output);
        return $output;
    }


    /**
     * Return all items in collection where value or key not in $arrays
     */
    public function diffAssoc(
        iterable ...$arrays
    ): static {
        $output = array_diff_assoc(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays
            )
        );

        return $this->propagate($output);
    }

    /**
     * DiffAssoc with custom key comparator
     */
    public function diffAssocBy(
        callable $keyCallback,
        iterable ...$arrays
    ): static {
        $output = array_diff_uassoc(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays,
                keyCallback: $keyCallback
            )
        );

        return $this->propagate($output);
    }

    /**
     * DiffAssoc with custom value comparator
     */
    public function diffAssocByValue(
        callable $valueCallback,
        iterable ...$arrays
    ): static {
        $output = array_udiff_assoc(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays,
                valueCallback: $valueCallback
            )
        );

        return $this->propagate($output);
    }

    /**
     * DiffAssoc with custom key and value comparator
     */
    public function diffAssocAll(
        callable $valueCallback,
        callable $keyCallback,
        iterable ...$arrays
    ): static {
        $output = array_udiff_uassoc(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays,
                valueCallback: $valueCallback,
                keyCallback: $keyCallback
            )
        );

        return $this->propagate($output);
    }

    /**
     * Return all items in collection where value not in $arrays
     */
    public function diffValues(
        iterable ...$arrays
    ): static {
        $output = array_diff(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays
            )
        );

        return $this->propagate($output);
    }

    /**
     * DiffValues with custom value comparator
     */
    public function diffValuesBy(
        callable $valueCallback,
        iterable ...$arrays
    ): static {
        $output = array_udiff(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays,
                valueCallback: $valueCallback
            )
        );

        return $this->propagate($output);
    }

    /**
     * Return all items in collection where key not in $arrays
     */
    public function diffKeys(
        iterable ...$arrays
    ): static {
        $output = array_diff_key(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays
            )
        );

        return $this->propagate($output);
    }

    /**
     * DiffKeys with custom key comparator
     */
    public function diffKeysBy(
        callable $keyCallback,
        iterable ...$arrays
    ): static {
        $output = array_diff_ukey(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays,
                keyCallback: $keyCallback
            )
        );

        return $this->propagate($output);
    }


    /**
     * Return all items in collection where value or key in $arrays
     */
    public function intersectAssoc(
        iterable ...$arrays
    ): static {
        $output = array_intersect_assoc(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays
            )
        );

        return $this->propagate($output);
    }

    /**
     * IntersectAssoc with custom key comparator
     */
    public function intersectAssocBy(
        callable $keyCallback,
        iterable ...$arrays
    ): static {
        $output = array_intersect_uassoc(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays,
                keyCallback: $keyCallback
            )
        );

        return $this->propagate($output);
    }

    /**
     * IntersectAssoc with custom value comparator
     */
    public function intersectAssocByValue(
        callable $valueCallback,
        iterable ...$arrays
    ): static {
        $output = array_uintersect_assoc(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays,
                valueCallback: $valueCallback
            )
        );

        return $this->propagate($output);
    }

    /**
     * IntersectAssoc with custom key and value comparator
     */
    public function intersectAssocAll(
        callable $valueCallback,
        callable $keyCallback,
        iterable ...$arrays
    ): static {
        $output = array_uintersect_uassoc(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays,
                valueCallback: $valueCallback,
                keyCallback: $keyCallback
            )
        );

        return $this->propagate($output);
    }

    /**
     * Return all items in collection where value in $arrays
     */
    public function intersectValues(
        iterable ...$arrays
    ): static {
        $output = array_intersect(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays
            )
        );

        return $this->propagate($output);
    }

    /**
     * IntersectValues with custom value comparator
     */
    public function intersectValuesBy(
        callable $valueCallback,
        iterable ...$arrays
    ): static {
        $output = array_uintersect(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays,
                valueCallback: $valueCallback
            )
        );

        return $this->propagate($output);
    }

    /**
     * Return all items in collection where key in $arrays
     */
    public function intersectKeys(
        iterable ...$arrays
    ): static {
        $output = array_intersect_key(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays
            )
        );

        return $this->propagate($output);
    }

    /**
     * IntersectKeys with custom key comparator
     */
    public function intersectKeysBy(
        callable $keyCallback,
        iterable ...$arrays
    ): static {
        $output = array_intersect_ukey(
            $this->items,
            // @phpstan-ignore-next-line
            ...ArrayUtils::mapArrayArgs(
                iterables: $arrays,
                keyCallback: $keyCallback
            )
        );

        return $this->propagate($output);
    }


    /**
     * Return subset of collection where callback returns true
     */
    public function filter(
        ?callable $callback = null
    ): static {
        if ($callback) {
            $output = array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);
        } else {
            $output = array_filter($this->items);
        }

        return $this->propagate($output);
    }

    /**
     * Combine collection with passed arrays via callback
     */
    public function map(
        callable $callback,
        iterable ...$arrays
    ): static {
        // @phpstan-ignore-next-line PHPStan bug
        return $this->propagate(array_map(
            $callback,
            $this->items,
            ...ArrayUtils::iterablesToArrays(...$arrays)
        ));
    }

    /**
     * Loop through collection to build new collection
     */
    public function mapSelf(
        callable $callback
    ): static {
        $callback = Closure::fromCallable($callback);

        if (new ReflectionFunction($callback)->isGenerator()) {
            $output = [];

            foreach ($this->items as $key => $value) {
                /**
                 * @var TKey $key
                 * @var Generator $iterable
                 * @phpstan-ignore-next-line
                 */
                $iterable = $callback($value, $key);
                $output = array_merge($output, iterator_to_array($iterable));
            }


            // @phpstan-ignore-next-line
            return $this->propagate($output);
        } else {
            $keys = array_keys($this->items);
            /** @var Closure(TValue,int|string):mixed $callback */
            $items = array_map($callback, $this->items, $keys);

            if (count($keys) !== count($items)) {
                throw Exceptional::UnexpectedValue(
                    message: 'Combine failed - key count does not match item count'
                );
            }

            // @phpstan-ignore-next-line
            return $this->propagate((array)array_combine($keys, $items));
        }
    }

    /**
     * Whittle down collection to single value
     */
    public function reduce(
        callable $callback,
        mixed $initial = null
    ): mixed {
        return array_reduce($this->items, $callback, $initial);
    }


    /**
     * Add all numeric values in collection
     */
    public function getSum(
        ?callable $filter = null
    ): float {
        return Coercion::asFloat(
            $this->reduce(function (float $result, $item) use ($filter) {
                if ($filter) {
                    $item = $filter($item);
                }

                if (!is_numeric($item)) {
                    return $result;
                }

                return $result + (float)$item;
            }, 0)
        );
    }

    /**
     * Multiple all numeric values in collection
     */
    public function getProduct(
        ?callable $filter = null
    ): float {
        return Coercion::asFloat(
            $this->reduce(function (float $result, $item) use ($filter) {
                if ($filter) {
                    $item = $filter($item);
                }

                if (!is_numeric($item)) {
                    return $result;
                }

                return $result * (float)$item;
            }, 1)
        );
    }

    /**
     * Get average value of numerics
     */
    public function getAvg(
        ?callable $filter = null
    ): ?float {
        if (!$count = count($this->items)) {
            return null;
        }

        return $this->getSum($filter) / $count;
    }


    /**
     * Combine a column with optional key column into single array
     */
    public function pluck(
        string $valueKey,
        ?string $indexKey = null
    ): array {
        return array_values(
            array_column($this->items, $valueKey, $indexKey)
        );
    }





    /**
     * Set by array access
     *
     * @param TValue $value
     */
    public function offsetSet(
        mixed $key,
        mixed $value
    ): void {
        if(!static::Mutable) {
            throw Exceptional::DomainException(
                message: 'Cannot modify immutable collection'
            );
        }

        if ($key === null) {
            if ($this instanceof SequenceInterface) {
                $this->push($value);
                return;
            } else {
                throw Exceptional::UnexpectedValue(
                    message: 'Cannot push to Maps'
                );
            }
        }

        $this->set($key, $value);
    }

    /**
     * Get by array access
     *
     * @return ?TValue
     */
    public function offsetGet(
        mixed $key
    ): mixed {
        return $this->get($key);
    }

    /**
     * Check by array access
     */
    public function offsetExists(
        mixed $key
    ): bool {
        return $this->has($key);
    }

    /**
     * Remove by array access
     */
    public function offsetUnset(
        mixed $key
    ): void {
        // @phpstan-ignore-next-line PHPStan bug
        $this->remove($key);
    }




    /**
     * Iterator interface
     *
     * @return Iterator<TKey,TValue>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Convert to array
     *
     * @return array<TKey,TValue>
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Convert to json
     *
     * @return array<int|string,mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }



    /**
     * Get dump info
     *
     * @return array<TKey,TValue>
     */
    public function __debugInfo(): array
    {
        return $this->items;
    }


    /**
     * Copy and reinitialise new object
     */
    abstract protected static function propagate(
        iterable $newItems = []
    ): static;
}
