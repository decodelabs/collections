<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

/**
 * @template TValue
 * @extends Collection<int, TValue>
 */
interface Sequence extends Collection, Sortable
{
    /**
     * @phpstan-return TValue|null
     */
    public function get(int $key);

    /**
     * @phpstan-return TValue|null
     */
    public function pull(int $key);

    /**
     * @phpstan-param TValue $value
     * @phpstan-return static<TValue>
     */
    public function set(int $key, $value): Sequence;

    /**
     * @phpstan-param TValue $value
     * @phpstan-return static<TValue>
     */
    public function put(int $key, $value): Sequence;

    public function has(int ...$keys): bool;
    public function hasAll(int ...$keys): bool;
    public function hasKey(int ...$keys): bool;
    public function hasKeys(int ...$keys): bool;

    /**
     * @phpstan-return static<TValue>
     */
    public function remove(int ...$keys): Sequence;

    /**
     * @phpstan-return static<TValue>
     */
    public function keep(int ...$keys): Sequence;


    /**
     * @phpstan-param TValue $value
     */
    public function findKey($value, bool $strict = false): ?int;


    /**
     * @phpstan-return static<TValue>
     */
    public function clear(): Sequence;

    /**
     * @phpstan-return static<TValue>
     */
    public function clearKeys(): Sequence;


    /**
     * @phpstan-return static<TValue>
     */
    public function collapse(bool $unique = false, bool $removeNull = false): Sequence;



    /**
     * @phpstan-param TValue $value
     * @phpstan-return static<TValue>
     */
    public function fill($value): Sequence;

    /**
     * @phpstan-param TValue $value
     * @phpstan-return static<TValue>
     */
    public static function createFill(int $length, $value): Sequence;


    /**
     * @param iterable<TValue> ...$arrays
     * @phpstan-return static<TValue>
     */
    public function merge(iterable ...$arrays): Sequence;

    /**
     * @param iterable<TValue> ...$arrays
     * @phpstan-return static<TValue>
     */
    public function mergeRecursive(iterable ...$arrays): Sequence;


    /**
     * @param iterable<TValue> ...$arrays
     * @phpstan-return static<TValue>
     */
    public function replace(iterable ...$arrays): Sequence;

    /**
     * @param iterable<TValue> ...$arrays
     * @phpstan-return static<TValue>
     */
    public function replaceRecursive(iterable ...$arrays): Sequence;


    /**
     * @phpstan-param TValue|null $value
     * @phpstan-return static<TValue>
     */
    public function padLeft(int $size, $value = null): Sequence;

    /**
     * @phpstan-param TValue|null $value
     * @phpstan-return static<TValue>
     */
    public function padRight(int $size, $value = null): Sequence;

    /**
     * @phpstan-param TValue|null $value
     * @phpstan-return static<TValue>
     */
    public function padBoth(int $size, $value = null): Sequence;


    /**
     * @param static<TValue>|null $removed
     * @phpstan-return static<TValue>
     */
    public function removeSlice(int $offset, int $length = null, Sequence &$removed = null): Sequence;

    /**
     * @param iterable<TValue> $replacement
     * @param static<TValue>|null $removed
     * @phpstan-return static<TValue>
     */
    public function replaceSlice(int $offset, int $length = null, iterable $replacement, Sequence &$removed = null): Sequence;


    /**
     * @phpstan-return static<TValue>
     */
    public function unique(int $flags = SORT_STRING): Sequence;


    /**
     * @param mixed $data
     * @phpstan-return static<TValue>
     */
    public function walk(callable $callback, $data = null): Sequence;

    /**
     * @param mixed $data
     * @phpstan-return static<TValue>
     */
    public function walkRecursive(callable $callback, $data = null): Sequence;


    /**
     * @return static<int>
     */
    public static function createRange(int $start, int $end, int $step = 1): Sequence;
}
