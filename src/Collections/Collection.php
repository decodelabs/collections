<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use Countable;
use DecodeLabs\Fluidity\Then;
use IteratorAggregate;
use JsonSerializable;

/**
 * @template TKey
 * @template TValue
 * @template TIterate = TValue
 *
 * @extends IteratorAggregate<TKey,TIterate>
 * @extends ArrayProvider<TKey,TValue>
 */
interface Collection extends
    ArrayProvider,
    JsonSerializable,
    Countable,
    IteratorAggregate,
    Then
{
    public function isEmpty(): bool;
    public function isMutable(): bool;

    public function copy(): static;

    /**
     * @return ?TValue
     */
    public function getFirst(
        ?callable $filter = null
    ): mixed;

    /**
     * @return ?TValue
     */
    public function getLast(
        ?callable $filter = null
    ): mixed;


    /**
     * @param TValue ...$values
     */
    public function push(
        mixed ...$values
    ): static;

    /**
     * @return ?TValue
     */
    public function pop(): mixed;

    /**
     * @param TValue ...$values
     */
    public function unshift(
        mixed ...$values
    ): static;

    /**
     * @return ?TValue
     */
    public function shift(): mixed;

    /**
     * @param TValue ...$values
     */
    public function append(
        mixed ...$values
    ): static;

    /**
     * @param TValue ...$values
     */
    public function prepend(
        mixed ...$values
    ): static;

    public function getRandom(): mixed;

    /**
     * @return list<TKey>
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
        ?int $length = null
    ): static;

    public function sliceRandom(
        int $number
    ): static;


    /**
     * @return array<int,static>
     */
    public function chunk(
        int $size
    ): array;

    /**
     * @return array<int,static>
     */
    public function chunkValues(
        int $size
    ): array;

    /**
     * @return MapInterface<TValue,int>
     */
    public function countValues(): MapInterface;


    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function diffAssoc(
        iterable ...$arrays
    ): static; // array_diff_assoc

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function diffAssocBy(
        callable $keyCallback,
        iterable ...$arrays
    ): static; // array_diff_uassoc

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function diffAssocByValue(
        callable $valueCallback,
        iterable ...$arrays
    ): static; // array_udiff_assoc

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function diffAssocAll(
        callable $valueCallback,
        callable $keyCallback,
        iterable ...$arrays
    ): static; // array_udiff_uassoc

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function diffValues(
        iterable ...$arrays
    ): static; // array_diff

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function diffValuesBy(
        callable $valueCallback,
        iterable ...$arrays
    ): static; // array_udiff

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function diffKeys(
        iterable ...$arrays
    ): static; // array_diff_key

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function diffKeysBy(
        callable $keyCallback,
        iterable ...$arrays
    ): static; // array_diff_ukey


    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function intersectAssoc(
        iterable ...$arrays
    ): static; // array_intersect_assoc

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function intersectAssocBy(
        callable $keyCallback,
        iterable ...$arrays
    ): static; // array_intersect_uassoc

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function intersectAssocByValue(
        callable $valueCallback,
        iterable ...$arrays
    ): static; // array_uintersect_assoc

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function intersectAssocAll(
        callable $valueCallback,
        callable $keyCallback,
        iterable ...$arrays
    ): static; // array_uintersect_uassoc

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function intersectValues(
        iterable ...$arrays
    ): static; // array_intersect

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function intersectValuesBy(
        callable $valueCallback,
        iterable ...$arrays
    ): static; // array_uintersect

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function intersectKeys(
        iterable ...$arrays
    ): static; // array_intersect_key

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function intersectKeysBy(
        callable $keyCallback,
        iterable ...$arrays
    ): static; // array_intersect_ukey


    public function filter(
        ?callable $callback = null
    ): static;


    /**
     * @param iterable<string|int,mixed> ...$arrays
     */
    public function map(
        callable $callback,
        iterable ...$arrays
    ): static;

    /**
     * @param callable(TValue,TKey):mixed $callback
     */
    public function mapSelf(
        callable $callback
    ): static;

    /**
     * @template TCarry
     * @param callable(TCarry,TIterate):TCarry $callback
     * @param TCarry $initial
     */
    public function reduce(
        callable $callback,
        mixed $initial = null
    ): mixed;


    public function getSum(
        ?callable $filter = null
    ): float;

    public function getProduct(
        ?callable $filter = null
    ): float;

    public function getAvg(
        ?callable $filter = null
    ): ?float;



    /**
     * @return list<mixed>
     */
    public function pluck(
        string $valueKey,
        ?string $indexKey = null
    ): array;
}
