<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

interface Sequence extends Collection, Sortable
{
    public function get(int $key);
    public function pull(int $key);
    public function set(int $key, $value): Sequence;
    public function put(int $key, $value): Sequence;
    public function has(int ...$keys): bool;
    public function hasAll(int ...$keys): bool;
    public function hasKey(int ...$keys): bool;
    public function hasKeys(int ...$keys): bool;
    public function remove(int ...$keys): Sequence;
    public function keep(int ...$keys): Sequence;

    public function findKey($value, bool $strict = false): ?int;

    public function clear(): Sequence;
    public function clearKeys(): Sequence;

    public function collapse(bool $unique = false, bool $removeNull = false): Sequence;

    public function push(...$values): Sequence;
    public function pop();
    public function unshift(...$values): Sequence;
    public function shift();
    public function append(...$values): Sequence;
    public function prepend(...$values): Sequence;

    public function fill($value): Sequence;
    public static function createFill(int $length, $value): Sequence;

    public function merge(iterable ...$arrays): Sequence;
    public function mergeRecursive(iterable ...$arrays): Sequence;

    public function replace(iterable ...$arrays): Sequence;
    public function replaceRecursive(iterable ...$arrays): Sequence;

    public function padLeft(int $size, $value = null): Sequence;
    public function padRight(int $size, $value = null): Sequence;
    public function padBoth(int $size, $value = null): Sequence;

    public function removeSlice(int $offset, int $length = null, Sequence &$removed = null): Sequence;
    public function replaceSlice(int $offset, int $length = null, iterable $replacement, Sequence &$removed = null): Sequence;

    public function unique(int $flags = SORT_STRING): Sequence;

    public function walk(callable $callback, $data = null): Sequence;
    public function walkRecursive(callable $callback, $data = null): Sequence;

    public static function createRange(int $start, int $end, int $step = 1): Sequence;
}
