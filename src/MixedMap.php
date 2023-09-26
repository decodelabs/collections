<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

/**
 * @template TValue
 * @template TIterate
 * @extends Collection<int|string, TValue, TIterate>
 */
interface MixedMap extends
    Collection,
    Sortable
{
    /**
     * @return TValue|null
     */
    public function get(int|string $key): mixed;

    /**
     * @return TValue|null
     */
    public function pull(int|string $key): mixed;

    /**
     * @param TValue $value
     */
    public function set(
        int|string $key,
        mixed $value
    ): static;

    public function has(int|string ...$keys): bool;
    public function hasAll(int|string ...$keys): bool;
    public function hasKey(int|string ...$keys): bool;
    public function hasKeys(int|string ...$keys): bool;
    public function remove(int|string ...$keys): static;
    public function keep(int|string ...$keys): static;


    /**
     * @param TValue $value
     */
    public function findKey(
        mixed $value,
        bool $strict = false
    ): int|string|null;

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


    public function changeKeyCase(int $case = CASE_LOWER): static;


    /**
     * @param iterable<string> $keys
     */
    public function combineWithKeys(iterable $keys): static;

    /**
     * @param iterable<TValue> $values
     */
    public function combineWithValues(iterable $values): static;


    /**
     * @param TValue $value
     */
    public function fill($value): static;

    /**
     * @return HashMap<int|string>
     */
    public function flip(): HashMap;


    /**
     * @param iterable<int|string, TValue> ...$arrays
     */
    public function merge(iterable ...$arrays): static;

    /**
     * @param iterable<int|string, TValue> ...$arrays
     */
    public function mergeRecursive(iterable ...$arrays): static;


    /**
     * @param iterable<int|string, TValue> ...$arrays
     */
    public function replace(iterable ...$arrays): static;

    /**
     * @param iterable<int|string, TValue> ...$arrays
     */
    public function replaceRecursive(iterable ...$arrays): static;


    /**
     * @param static<TValue, TIterate>|null $removed
     */
    public function removeSlice(
        int $offset,
        int $length = null,
        MixedMap &$removed = null
    ): static;

    /**
     * @param iterable<int|string, TValue> $replacement
     * @param static<TValue, TIterate>|null $removed
     */
    public function replaceSlice(
        int $offset,
        int $length = null,
        iterable $replacement,
        ?MixedMap &$removed = null
    ): static;

    public function unique(int $flags = SORT_STRING): static;

    public function walk(
        callable $callback,
        mixed $data = null
    ): static;

    public function walkRecursive(
        callable $callback,
        mixed $data = null
    ): static;
}
