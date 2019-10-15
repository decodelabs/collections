<?php
/**
 * This file is part of the Collections package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Collections;

use DecodeLabs\Gadgets\Then;

use Traversable;
use JsonSerializable;
use Countable;
use ArrayAccess;

interface Collection extends Traversable, ArrayProvider, JsonSerializable, Countable, ArrayAccess, Then
{
    public function isEmpty(): bool;
    public function isMutable(): bool;
    public function copy(): Collection;

    public function getFirst(callable $filter=null);
    public function getLast(callable $filter=null);
    public function getRandom();

    public function getKeys(): Collection;

    public function contains($value, bool $strict=false): bool;
    public function containsRecursive($value, bool $strict=false): bool;

    public function slice(int $offset, int $length=null): Collection;
    public function sliceRandom(int $number): Collection;

    public function chunk(int $size): Collection;
    public function chunkValues(int $size): Collection;

    public function countValues(): Collection;

    public function diffAssoc(iterable ...$arrays): Collection; // array_diff_assoc
    public function diffAssocBy(callable $keyCallback, iterable ...$arrays): Collection; // array_diff_uassoc
    public function diffAssocByValue(callable $valueCallback, iterable ...$arrays): Collection; // array_udiff_assoc
    public function diffAssocAll(callable $valueCallback, callable $keyCallback, iterable ...$arrays): Collection; // array_udiff_uassoc
    public function diffValues(iterable ...$arrays): Collection; // array_diff
    public function diffValuesBy(callable $valueCallback, iterable ...$arrays): Collection; // array_udiff
    public function diffKeys(iterable ...$arrays): Collection; // array_diff_key
    public function diffKeysBy(callable $keyCallback, iterable ...$arrays): Collection; // array_diff_ukey

    public function intersectAssoc(iterable ...$arrays): Collection; // array_intersect_assoc
    public function intersectAssocBy(callable $keyCallback, iterable ...$arrays): Collection; // array_intersect_uassoc
    public function intersectAssocByValue(callable $valueCallback, iterable ...$arrays): Collection; // array_uintersect_assoc
    public function intersectAssocAll(callable $valueCallback, callable $keyCallback, iterable ...$arrays): Collection; // array_uintersect_uassoc
    public function intersectValues(iterable ...$arrays): Collection; // array_intersect
    public function intersectValuesBy(callable $valueCallback, iterable ...$arrays): Collection; // array_uintersect
    public function intersectKeys(iterable ...$arrays): Collection; // array_intersect_key
    public function intersectKeysBy(callable $keyCallback, iterable ...$arrays): Collection; // array_intersect_ukey

    public function filter(callable $callback=null): Collection;
    public function map(callable $callback, iterable ...$arrays): Collection;
    public function mapSelf(callable $callback): Collection;
    public function reduce(callable $callback, $initial=null);

    public function getSum(callable $filter=null);
    public function getProduct(callable $filter=null);
    public function getAvg(callable $filter=null);

    public function pluck(string $valueKey, string $indexKey=null): Collection;
}
