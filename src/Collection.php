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
interface Collection extends
    ArrayProvider,
    JsonSerializable,
    Countable,
    ArrayAccess,
    Then
{
    public function isEmpty(): bool;
    public function isMutable(): bool;

    /**
     * @phpstan-return static<TKey, TValue>
     */
    public function copy(): Collection;

    /**
     * @phpstan-return TValue|null
     */
    public function getFirst(callable $filter = null): mixed;

    /**
     * @phpstan-return TValue|null
     */
    public function getLast(callable $filter = null): mixed;


    /**
     * @phpstan-param TValue ...$values
     * @phpstan-return static<TKey, TValue>
     */
    public function push(mixed ...$values): Collection;

    /**
     * @phpstan-return TValue|null
     */
    public function pop(): mixed;

    /**
     * @phpstan-param TValue ...$values
     * @phpstan-return static<TKey, TValue>
     */
    public function unshift(mixed ...$values): Collection;

    /**
     * @phpstan-return TValue|null
     */
    public function shift(): mixed;

    /**
     * @phpstan-param TValue ...$values
     * @phpstan-return static<TKey, TValue>
     */
    public function append(mixed ...$values): Collection;

    /**
     * @phpstan-param TValue ...$values
     * @phpstan-return static<TKey, TValue>
     */
    public function prepend(mixed ...$values): Collection;

    public function getRandom(): mixed;

    /**
     * @phpstan-return array<TKey>
     */
    public function getKeys(): array;

    public function contains(
        mixed $value,
        bool $strict = false
    ): bool;

    public function containsRecursive(
        mixed $value,
        bool $strict = false
    ): bool;


    /**
     * @phpstan-return static<TKey, TValue>
     */
    public function slice(
        int $offset,
        int $length = null
    ): Collection;

    /**
     * @phpstan-return static<TKey, TValue>
     */
    public function sliceRandom(int $number): Collection;


    /**
     * @phpstan-return array<int, static<TKey, TValue>>
     */
    public function chunk(int $size): array;

    /**
     * @phpstan-return array<int, static<TKey, TValue>>
     */
    public function chunkValues(int $size): array;

    /**
     * @phpstan-return array<TKey, int>
     */
    public function countValues(): array;


    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function diffAssoc(iterable ...$arrays): Collection; // array_diff_assoc

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function diffAssocBy(
        callable $keyCallback,
        iterable ...$arrays
    ): Collection; // array_diff_uassoc

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function diffAssocByValue(
        callable $valueCallback,
        iterable ...$arrays
    ): Collection; // array_udiff_assoc

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function diffAssocAll(
        callable $valueCallback,
        callable $keyCallback,
        iterable ...$arrays
    ): Collection; // array_udiff_uassoc

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function diffValues(iterable ...$arrays): Collection; // array_diff

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function diffValuesBy(
        callable $valueCallback,
        iterable ...$arrays
    ): Collection; // array_udiff

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function diffKeys(iterable ...$arrays): Collection; // array_diff_key

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function diffKeysBy(
        callable $keyCallback,
        iterable ...$arrays
    ): Collection; // array_diff_ukey


    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function intersectAssoc(iterable ...$arrays): Collection; // array_intersect_assoc

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function intersectAssocBy(
        callable $keyCallback,
        iterable ...$arrays
    ): Collection; // array_intersect_uassoc

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function intersectAssocByValue(
        callable $valueCallback,
        iterable ...$arrays
    ): Collection; // array_uintersect_assoc

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function intersectAssocAll(
        callable $valueCallback,
        callable $keyCallback,
        iterable ...$arrays
    ): Collection; // array_uintersect_uassoc

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function intersectValues(iterable ...$arrays): Collection; // array_intersect

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function intersectValuesBy(
        callable $valueCallback,
        iterable ...$arrays
    ): Collection; // array_uintersect

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function intersectKeys(iterable ...$arrays): Collection; // array_intersect_key

    /**
     * @phpstan-param iterable<TKey, TValue> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function intersectKeysBy(
        callable $keyCallback,
        iterable ...$arrays
    ): Collection; // array_intersect_ukey


    /**
     * @phpstan-return static<TKey, TValue>
     */
    public function filter(callable $callback = null): Collection;

    /**
     * @param iterable<string|int, mixed> ...$arrays
     * @phpstan-return static<TKey, TValue>
     */
    public function map(
        callable $callback,
        iterable ...$arrays
    ): Collection;

    /**
     * @phpstan-return static<TKey, TValue>
     */
    public function mapSelf(callable $callback): Collection;

    public function reduce(
        callable $callback,
        mixed $initial = null
    ): mixed;


    public function getSum(callable $filter = null): float;
    public function getProduct(callable $filter = null): float;
    public function getAvg(callable $filter = null): ?float;


    /**
     * @return array<mixed>
     */
    public function pluck(
        string $valueKey,
        string $indexKey = null
    ): array;
}
