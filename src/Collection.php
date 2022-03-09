<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use ArrayAccess;
use Countable;
use DecodeLabs\Fluidity\Then;
use JsonSerializable;

/**
 * @template TKey
 * @template TValue
 *
 * @extends ArrayProvider<TKey, TValue>
 * @extends ArrayAccess<TKey, TValue>
 */
interface Collection extends ArrayProvider, JsonSerializable, Countable, ArrayAccess, Then
{
    public function isEmpty(): bool;
    public function isMutable(): bool;

    /**
     * @return static<TKey, TValue>
     */
    public function copy(): Collection;

    /**
     * @return TValue|null
     */
    public function getFirst(callable $filter = null);

    /**
     * @return TValue|null
     */
    public function getLast(callable $filter = null);


    /**
     * @param TValue ...$values
     * @return static<TKey, TValue>
     */
    public function push(...$values): Collection;

    /**
     * @return TValue|null
     */
    public function pop();

    /**
     * @param TValue ...$values
     * @return static<TKey, TValue>
     */
    public function unshift(...$values): Collection;

    /**
     * @return TValue|null
     */
    public function shift();

    /**
     * @param TValue ...$values
     * @return static<TKey, TValue>
     */
    public function append(...$values): Collection;

    /**
     * @param TValue ...$values
     * @return static<TKey, TValue>
     */
    public function prepend(...$values): Collection;


    /**
     * @return mixed
     */
    public function getRandom();


    /**
     * @return array<TKey>
     */
    public function getKeys(): array;


    /**
     * @param mixed $value
     */
    public function contains($value, bool $strict = false): bool;

    /**
     * @param mixed $value
     */
    public function containsRecursive($value, bool $strict = false): bool;


    /**
     * @return static<TKey, TValue>
     */
    public function slice(int $offset, int $length = null): Collection;

    /**
     * @return static<TKey, TValue>
     */
    public function sliceRandom(int $number): Collection;


    /**
     * @return array<int, static<TKey, TValue>>
     */
    public function chunk(int $size): array;

    /**
     * @return array<int, static<TKey, TValue>>
     */
    public function chunkValues(int $size): array;

    /**
     * @return array<TKey, int>
     */
    public function countValues(): array;


    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function diffAssoc(iterable ...$arrays): Collection; // array_diff_assoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function diffAssocBy(callable $keyCallback, iterable ...$arrays): Collection; // array_diff_uassoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function diffAssocByValue(callable $valueCallback, iterable ...$arrays): Collection; // array_udiff_assoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function diffAssocAll(callable $valueCallback, callable $keyCallback, iterable ...$arrays): Collection; // array_udiff_uassoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function diffValues(iterable ...$arrays): Collection; // array_diff

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function diffValuesBy(callable $valueCallback, iterable ...$arrays): Collection; // array_udiff

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function diffKeys(iterable ...$arrays): Collection; // array_diff_key

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function diffKeysBy(callable $keyCallback, iterable ...$arrays): Collection; // array_diff_ukey


    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function intersectAssoc(iterable ...$arrays): Collection; // array_intersect_assoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function intersectAssocBy(callable $keyCallback, iterable ...$arrays): Collection; // array_intersect_uassoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function intersectAssocByValue(callable $valueCallback, iterable ...$arrays): Collection; // array_uintersect_assoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function intersectAssocAll(callable $valueCallback, callable $keyCallback, iterable ...$arrays): Collection; // array_uintersect_uassoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function intersectValues(iterable ...$arrays): Collection; // array_intersect

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function intersectValuesBy(callable $valueCallback, iterable ...$arrays): Collection; // array_uintersect

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function intersectKeys(iterable ...$arrays): Collection; // array_intersect_key

    /**
     * @param iterable<TKey, TValue> ...$arrays
     * @return static<TKey, TValue>
     */
    public function intersectKeysBy(callable $keyCallback, iterable ...$arrays): Collection; // array_intersect_ukey


    /**
     * @return static<TKey, TValue>
     */
    public function filter(callable $callback = null): Collection;

    /**
     * @param iterable<string|int, mixed> ...$arrays
     * @return static<TKey, TValue>
     */
    public function map(callable $callback, iterable ...$arrays): Collection;

    /**
     * @return static<TKey, TValue>
     */
    public function mapSelf(callable $callback): Collection;

    /**
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial = null);


    public function getSum(callable $filter = null): float;
    public function getProduct(callable $filter = null): float;
    public function getAvg(callable $filter = null): ?float;


    /**
     * @return array<mixed>
     */
    public function pluck(string $valueKey, string $indexKey = null): array;
}
