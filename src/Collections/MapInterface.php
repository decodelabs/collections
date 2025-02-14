<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

/**
 * @template TKey
 * @template TValue
 * @template TIterate = TValue
 * @template TFlipIterate = TKey
 * @extends Collection<TKey,TValue,TIterate>
 */
interface MapInterface extends
    Collection,
    Sortable
{
    /**
     * @param TKey $key
     * @return TValue|null
     */
    public function get(
        mixed $key
    ): mixed;

    /**
     * @param TKey $key
     * @return TValue|null
     */
    public function pull(
        mixed $key
    ): mixed;

    /**
     * @param TKey $key
     * @param TValue $value
     */
    public function set(
        mixed $key,
        mixed $value
    ): static;

    /**
     * @param TKey ...$keys
     */
    public function has(
        mixed ...$keys
    ): bool;

    /**
     * @param TKey ...$keys
     */
    public function hasAll(
        mixed ...$keys
    ): bool;

    /**
     * @param TKey ...$keys
     */
    public function hasKey(
        mixed ...$keys
    ): bool;

    /**
     * @param TKey ...$keys
     */
    public function hasKeys(
        mixed ...$keys
    ): bool;

    /**
     * @param TKey ...$keys
     */
    public function remove(
        mixed ...$keys
    ): static;

    /**
     * @param TKey ...$keys
     */
    public function keep(
        mixed ...$keys
    ): static;



    /**
     * @param TValue $value
     * @return ?TKey
     */
    public function findKey(
        mixed $value,
        bool $strict = false
    ): mixed;

    public function clear(): static;
    public function clearKeys(): static;

    public function collapse(
        bool $unique = false,
        bool $removeNull = false
    ): static;

    public function collapseValues(
        bool $unique = false,
        bool $removeNull = false
    ): static;


    public function changeKeyCase(
        int $case = CASE_LOWER
    ): static;


    /**
     * @param iterable<TKey> $keys
     */
    public function combineWithKeys(
        iterable $keys
    ): static;

    /**
     * @param iterable<TValue> $values
     */
    public function combineWithValues(
        iterable $values
    ): static;


    /**
     * @param TValue $value
     */
    public function fill(
        mixed $value
    ): static;

    /**
     * @return MapInterface<TValue,TKey,TFlipIterate>
     */
    public function flip(): MapInterface;


    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function merge(
        iterable ...$arrays
    ): static;

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function mergeRecursive(
        iterable ...$arrays
    ): static;


    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function replace(
        iterable ...$arrays
    ): static;

    /**
     * @param iterable<TKey,TValue> ...$arrays
     */
    public function replaceRecursive(
        iterable ...$arrays
    ): static;


    /**
     * @param static<TKey,TValue,TIterate>|null $removed
     */
    public function removeSlice(
        int $offset,
        ?int $length = null,
        ?MapInterface &$removed = null
    ): static;

    /**
     * @param iterable<TKey,TValue> $replacement
     * @param ?static<TKey,TValue,TIterate> $removed
     */
    public function replaceSlice(
        int $offset,
        ?int $length,
        iterable $replacement,
        ?MapInterface &$removed = null
    ): static;

    public function unique(
        int $flags = SORT_STRING
    ): static;

    public function walk(
        callable $callback,
        mixed $data = null
    ): static;

    public function walkRecursive(
        callable $callback,
        mixed $data = null
    ): static;
}
