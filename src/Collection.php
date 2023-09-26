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
use IteratorAggregate;
use JsonSerializable;

/**
 * @template TKey
 * @template TValue
 * @template TIterate of mixed|TValue
 *
 * @extends IteratorAggregate<TKey, TIterate>
 * @extends ArrayProvider<TKey, TValue>
 * @extends ArrayAccess<TKey, TValue>
 */
interface Collection extends
    ArrayProvider,
    JsonSerializable,
    Countable,
    ArrayAccess,
    IteratorAggregate,
    Then
{
    public function isEmpty(): bool;
    public function isMutable(): bool;

    public function copy(): static;

    /**
     * @return TValue|null
     */
    public function getFirst(callable $filter = null): mixed;

    /**
     * @return TValue|null
     */
    public function getLast(callable $filter = null): mixed;


    /**
     * @param TValue ...$values
     */
    public function push(mixed ...$values): static;

    /**
     * @return TValue|null
     */
    public function pop(): mixed;

    /**
     * @param TValue ...$values
     */
    public function unshift(mixed ...$values): static;

    /**
     * @return TValue|null
     */
    public function shift(): mixed;

    /**
     * @param TValue ...$values
     */
    public function append(mixed ...$values): static;

    /**
     * @param TValue ...$values
     */
    public function prepend(mixed ...$values): static;

    public function getRandom(): mixed;

    /**
     * @return array<TKey>
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


    public function slice(
        int $offset,
        int $length = null
    ): static;

    public function sliceRandom(int $number): static;


    /**
     * @return array<int, static<TKey, TValue, TIterate>>
     */
    public function chunk(int $size): array;

    /**
     * @return array<int, static<TKey, TValue, TIterate>>
     */
    public function chunkValues(int $size): array;

    /**
     * @return array<TKey, int>
     */
    public function countValues(): array;


    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function diffAssoc(iterable ...$arrays): static; // array_diff_assoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function diffAssocBy(
        callable $keyCallback,
        iterable ...$arrays
    ): static; // array_diff_uassoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function diffAssocByValue(
        callable $valueCallback,
        iterable ...$arrays
    ): static; // array_udiff_assoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function diffAssocAll(
        callable $valueCallback,
        callable $keyCallback,
        iterable ...$arrays
    ): static; // array_udiff_uassoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function diffValues(iterable ...$arrays): static; // array_diff

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function diffValuesBy(
        callable $valueCallback,
        iterable ...$arrays
    ): static; // array_udiff

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function diffKeys(iterable ...$arrays): static; // array_diff_key

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function diffKeysBy(
        callable $keyCallback,
        iterable ...$arrays
    ): static; // array_diff_ukey


    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function intersectAssoc(iterable ...$arrays): static; // array_intersect_assoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function intersectAssocBy(
        callable $keyCallback,
        iterable ...$arrays
    ): static; // array_intersect_uassoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function intersectAssocByValue(
        callable $valueCallback,
        iterable ...$arrays
    ): static; // array_uintersect_assoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function intersectAssocAll(
        callable $valueCallback,
        callable $keyCallback,
        iterable ...$arrays
    ): static; // array_uintersect_uassoc

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function intersectValues(iterable ...$arrays): static; // array_intersect

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function intersectValuesBy(
        callable $valueCallback,
        iterable ...$arrays
    ): static; // array_uintersect

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function intersectKeys(iterable ...$arrays): static; // array_intersect_key

    /**
     * @param iterable<TKey, TValue> ...$arrays
     */
    public function intersectKeysBy(
        callable $keyCallback,
        iterable ...$arrays
    ): static; // array_intersect_ukey


    public function filter(callable $callback = null): static;

    /**
     * @param iterable<string|int, mixed> ...$arrays
     */
    public function map(
        callable $callback,
        iterable ...$arrays
    ): static;

    public function mapSelf(callable $callback): static;

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
