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
interface HashMap extends Collection, Sortable
{
    /**
     * @param int|string $key
     * @return TValue|null
     */
    public function get($key);

    /**
     * @param int|string $key
     * @return TValue|null
     */
    public function pull($key);

    /**
     * @param int|string $key
     * @param TValue $value
     * @return static<TValue>
     */
    public function set($key, $value): HashMap;

    /**
     * @param int|string ...$keys
     */
    public function has(...$keys): bool;

    /**
     * @param int|string ...$keys
     */
    public function hasAll(...$keys): bool;

    /**
     * @param int|string ...$keys
     */
    public function hasKey(...$keys): bool;

    /**
     * @param int|string ...$keys
     */
    public function hasKeys(...$keys): bool;

    /**
     * @param int|string ...$keys
     * @return static<TValue>
     */
    public function remove(...$keys): HashMap;

    /**
     * @param int|string ...$keys
     * @return static<TValue>
     */
    public function keep(...$keys): HashMap;


    /**
     * @param TValue $value
     * @return int|string|null
     */
    public function findKey($value, bool $strict = false);

    /**
     * @return static<TValue>
     */
    public function clear(): HashMap;

    /**
     * @return static<TValue>
     */
    public function clearKeys(): HashMap;


    /**
     * @return static<TValue>
     */
    public function collapse(bool $unique = false, bool $removeNull = false): HashMap;

    /**
     * @return static<TValue>
     */
    public function collapseValues(bool $unique = false, bool $removeNull = false): HashMap;



    /**
     * @return static<TValue>
     */
    public function changeKeyCase(int $case = CASE_LOWER): HashMap;


    /**
     * @param iterable<string> $keys
     * @return static<TValue>
     */
    public function combineWithKeys(iterable $keys): HashMap;

    /**
     * @param iterable<TValue> $values
     * @return static<TValue>
     */
    public function combineWithValues(iterable $values): HashMap;


    /**
     * @param TValue $value
     * @return static<TValue>
     */
    public function fill($value): HashMap;

    /**
     * @return static<int|string>
     */
    public function flip(): HashMap;


    /**
     * @param iterable<int|string, TValue> ...$arrays
     * @return static<TValue>
     */
    public function merge(iterable ...$arrays): HashMap;

    /**
     * @param iterable<int|string, TValue> ...$arrays
     * @return static<TValue>
     */
    public function mergeRecursive(iterable ...$arrays): HashMap;


    /**
     * @param iterable<int|string, TValue> ...$arrays
     * @return static<TValue>
     */
    public function replace(iterable ...$arrays): HashMap;

    /**
     * @param iterable<int|string, TValue> ...$arrays
     * @return static<TValue>
     */
    public function replaceRecursive(iterable ...$arrays): HashMap;


    /**
     * @param static<TValue>|null $removed
     * @return static<TValue>
     */
    public function removeSlice(int $offset, int $length = null, HashMap &$removed = null): HashMap;

    /**
     * @param iterable<int|string, TValue> $replacement
     * @param static<TValue>|null $removed
     * @return static<TValue>
     */
    public function replaceSlice(int $offset, int $length = null, iterable $replacement, HashMap &$removed = null): HashMap;

    /**
     * @return static<TValue>
     */
    public function unique(int $flags = SORT_STRING): HashMap;


    /**
     * @param mixed $data
     * @return static<TValue>
     */
    public function walk(callable $callback, $data = null): HashMap;

    /**
     * @param mixed $data
     * @return static<TValue>
     */
    public function walkRecursive(callable $callback, $data = null): HashMap;
}
