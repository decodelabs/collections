<?php
/**
 * This file is part of the Collections package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Collections;

/**
 * All methods returning Readable MUST be immutable,
 * regardless of whether implementation is mutable
 */
interface Readable extends Collection, \Countable, \ArrayAccess
{
    public function getFirst(callable $filter=null);
    public function getLast(callable $filter=null);
    public function getRandom();

    public function getKeys(): Readable;

    public function contains($value, bool $strict=false): bool;
    public function containsRecursive($value, bool $strict=false): bool;

    public function slice(int $offset, int $length=null): Readable;
    public function sliceRandom(int $number): Readable;

    public function chunk(int $size): Readable;
    public function chunkValues(int $size): Readable;

    public function countValues(): Readable;

    public function diffAssoc(iterable ...$arrays): Readable; // array_diff_assoc
    public function diffAssocBy(callable $keyCallback, iterable ...$arrays): Readable; // array_diff_uassoc
    public function diffAssocByValue(callable $valueCallback, iterable ...$arrays): Readable; // array_udiff_assoc
    public function diffAssocAll(callable $valueCallback, callable $keyCallback, iterable ...$arrays): Readable; // array_udiff_uassoc
    public function diffValues(iterable ...$arrays): Readable; // array_diff
    public function diffValuesBy(callable $valueCallback, iterable ...$arrays): Readable; // array_udiff
    public function diffKeys(iterable ...$arrays): Readable; // array_diff_key
    public function diffKeysBy(callable $keyCallback, iterable ...$arrays): Readable; // array_diff_ukey

    public function intersectAssoc(iterable ...$arrays): Readable; // array_intersect_assoc
    public function intersectAssocBy(callable $keyCallback, iterable ...$arrays): Readable; // array_intersect_uassoc
    public function intersectAssocByValue(callable $valueCallback, iterable ...$arrays): Readable; // array_uintersect_assoc
    public function intersectAssocAll(callable $valueCallback, callable $keyCallback, iterable ...$arrays): Readable; // array_uintersect_uassoc
    public function intersectValues(iterable ...$arrays): Readable; // array_intersect
    public function intersectValuesBy(callable $valueCallback, iterable ...$arrays): Readable; // array_uintersect
    public function intersectKeys(iterable ...$arrays): Readable; // array_intersect_key
    public function intersectKeysBy(callable $keyCallback, iterable ...$arrays): Readable; // array_intersect_ukey

    public function filter(callable $callback=null): Readable;
    public function map(callable $callback, iterable ...$arrays): Readable;
    public function mapSelf(callable $callback): Readable;
    public function reduce(callable $callback, $initial=null);

    public function getSum(callable $filter=null);
    public function getProduct(callable $filter=null);
    public function getAvg(callable $filter=null);

    public function pluck(string $valueKey, string $indexKey=null): Readable;
}
