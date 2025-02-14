<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

/**
 * @template TValue
 * @extends Collection<int,TValue>
 */
interface SequenceInterface extends
    Collection,
    Sortable
{
    /**
     * @return TValue|null
     */
    public function get(
        int $key
    ): mixed;

    /**
     * @return TValue|null
     */
    public function pull(
        int $key
    ): mixed;

    /**
     * @param TValue $value
     */
    public function set(
        int $key,
        mixed $value
    ): static;

    /**
     * @param TValue $value
     */
    public function put(
        int $key,
        mixed $value
    ): static;

    public function has(
        int ...$keys
    ): bool;

    public function hasAll(
        int ...$keys
    ): bool;

    public function hasKey(
        int ...$keys
    ): bool;

    public function hasKeys(
        int ...$keys
    ): bool;

    public function remove(
        int ...$keys
    ): static;

    public function keep(
        int ...$keys
    ): static;



    /**
     * @param TValue $value
     */
    public function findKey(
        mixed $value,
        bool $strict = false
    ): ?int;


    public function clear(): static;
    public function clearKeys(): static;

    public function collapse(
        bool $unique = false,
        bool $removeNull = false
    ): static;



    /**
     * @param TValue $value
     */
    public function fill(
        mixed $value
    ): static;

    /**
     * @param TValue $value
     */
    public static function createFill(
        int $length,
        mixed $value
    ): static;


    /**
     * @param iterable<TValue> ...$arrays
     */
    public function merge(
        iterable ...$arrays
    ): static;

    /**
     * @param iterable<TValue> ...$arrays
     */
    public function mergeRecursive(
        iterable ...$arrays
    ): static;


    /**
     * @param iterable<TValue> ...$arrays
     */
    public function replace(
        iterable ...$arrays
    ): static;

    /**
     * @param iterable<TValue> ...$arrays
     */
    public function replaceRecursive(
        iterable ...$arrays
    ): static;


    /**
     * @param TValue|null $value
     */
    public function padLeft(
        int $size,
        mixed $value = null
    ): static;

    /**
     * @param TValue|null $value
     */
    public function padRight(
        int $size,
        mixed $value = null
    ): static;

    /**
     * @param TValue|null $value
     */
    public function padBoth(
        int $size,
        mixed $value = null
    ): static;


    /**
     * @param static<TValue>|null $removed
     */
    public function removeSlice(
        int $offset,
        ?int $length = null,
        ?SequenceInterface &$removed = null
    ): static;

    /**
     * @param iterable<TValue> $replacement
     * @param static<TValue>|null $removed
     */
    public function replaceSlice(
        int $offset,
        ?int $length,
        iterable $replacement,
        ?SequenceInterface &$removed = null
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

    public static function createRange(
        int $start,
        int $end,
        int $step = 1
    ): static;
}
