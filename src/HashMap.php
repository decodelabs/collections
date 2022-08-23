<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

/**
 * @template TValue
 * @extends Collection<int|string, TValue>
 */
interface HashMap extends
    Collection,
    Sortable
{
    /**
     * @phpstan-return TValue|null
     */
    public function get(int|string $key): mixed;

    /**
     * @phpstan-return TValue|null
     */
    public function pull(int|string $key): mixed;

    /**
     * @phpstan-param TValue $value
     * @phpstan-return static<TValue>
     */
    public function set(
        int|string $key,
        mixed $value
    ): HashMap;

    public function has(int|string ...$keys): bool;
    public function hasAll(int|string ...$keys): bool;
    public function hasKey(int|string ...$keys): bool;
    public function hasKeys(int|string ...$keys): bool;

    /**
     * @phpstan-return static<TValue>
     */
    public function remove(int|string ...$keys): HashMap;

    /**
     * @phpstan-return static<TValue>
     */
    public function keep(int|string ...$keys): HashMap;


    /**
     * @phpstan-param TValue $value
     */
    public function findKey(
        mixed $value,
        bool $strict = false
    ): int|string|null;

    /**
     * @phpstan-return static<TValue>
     */
    public function clear(): HashMap;

    /**
     * @phpstan-return static<TValue>
     */
    public function clearKeys(): HashMap;


    /**
     * @phpstan-return static<TValue>
     */
    public function collapse(
        bool $unique = false,
        bool $removeNull = false
    ): HashMap;

    /**
     * @phpstan-return static<TValue>
     */
    public function collapseValues(
        bool $unique = false,
        bool $removeNull = false
    ): HashMap;



    /**
     * @phpstan-return static<TValue>
     */
    public function changeKeyCase(int $case = CASE_LOWER): HashMap;


    /**
     * @param iterable<string> $keys
     * @phpstan-return static<TValue>
     */
    public function combineWithKeys(iterable $keys): HashMap;

    /**
     * @param iterable<TValue> $values
     * @phpstan-return static<TValue>
     */
    public function combineWithValues(iterable $values): HashMap;


    /**
     * @phpstan-param TValue $value
     * @phpstan-return static<TValue>
     */
    public function fill($value): HashMap;

    /**
     * @return static<int|string>
     */
    public function flip(): HashMap;


    /**
     * @param iterable<int|string, TValue> ...$arrays
     * @phpstan-return static<TValue>
     */
    public function merge(iterable ...$arrays): HashMap;

    /**
     * @param iterable<int|string, TValue> ...$arrays
     * @phpstan-return static<TValue>
     */
    public function mergeRecursive(iterable ...$arrays): HashMap;


    /**
     * @param iterable<int|string, TValue> ...$arrays
     * @phpstan-return static<TValue>
     */
    public function replace(iterable ...$arrays): HashMap;

    /**
     * @param iterable<int|string, TValue> ...$arrays
     * @phpstan-return static<TValue>
     */
    public function replaceRecursive(iterable ...$arrays): HashMap;


    /**
     * @param static<TValue>|null $removed
     * @phpstan-return static<TValue>
     */
    public function removeSlice(
        int $offset,
        int $length = null,
        HashMap &$removed = null
    ): HashMap;

    /**
     * @param iterable<int|string, TValue> $replacement
     * @param static<TValue>|null $removed
     * @phpstan-return static<TValue>
     */
    public function replaceSlice(
        int $offset,
        int $length = null,
        iterable $replacement,
        HashMap &$removed = null
    ): HashMap;

    /**
     * @phpstan-return static<TValue>
     */
    public function unique(int $flags = SORT_STRING): HashMap;


    /**
     * @phpstan-return static<TValue>
     */
    public function walk(
        callable $callback,
        mixed $data = null
    ): HashMap;

    /**
     * @phpstan-return static<TValue>
     */
    public function walkRecursive(
        callable $callback,
        mixed $data = null
    ): HashMap;
}
