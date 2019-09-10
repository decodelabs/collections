<?php
/**
 * This file is part of the Collections package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Collections;

interface HashMap extends Readable, Sortable
{
    public function get(string $key);
    public function pull(string $key);
    public function set(string $key, $value): HashMap;
    public function has(string ...$keys): bool;
    public function hasAll(string ...$keys): bool;
    public function hasKey(string ...$keys): bool;
    public function hasKeys(string ...$keys): bool;
    public function remove(string ...$keys): HashMap;
    public function keep(string ...$keys): HashMap;

    public function findKey($value, bool $strict=false): ?string;

    public function clear(): HashMap;
    public function clearKeys(): HashMap;

    public function collapse(bool $unique=false, bool $removeNull=false): HashMap;
    public function collapseValues(bool $unique=false, bool $removeNull=false): HashMap;

    public function pop();
    public function shift();

    public function changeKeyCase(int $case=CASE_LOWER): HashMap;

    public function combineWithKeys(iterable $keys): HashMap;
    public function combineWithValues(iterable $values): HashMap;

    public function fill($value): HashMap;
    public function flip(): HashMap;

    public function merge(iterable ...$arrays): HashMap;
    public function mergeRecursive(iterable ...$arrays): HashMap;

    public function replace(iterable ...$arrays): HashMap;
    public function replaceRecursive(iterable ...$arrays): HashMap;

    public function removeSlice(int $offset, int $length=null, HashMap &$removed=null): HashMap;
    public function replaceSlice(int $offset, int $length=null, iterable $replacement, HashMap &$removed=null): HashMap;

    public function unique(int $flags=SORT_STRING): HashMap;

    public function walk(callable $callback, $data=null): HashMap;
    public function walkRecursive(callable $callback, $data=null): HashMap;
}
