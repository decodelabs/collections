<?php
/**
 * This file is part of the Collections package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Collections;

interface Sortable
{
    public function sort(int $flags=\SORT_REGULAR): Sortable; // asort
    public function reverseSort(int $flags=\SORT_REGULAR): Sortable; // arsort
    public function sortBy(callable $callable): Sortable; // uasort

    public function sortNatural(): Sortable; // natsort
    public function sortCaseNatural(): Sortable; // natcasesort

    public function sortValues(int $flags=\SORT_REGULAR): Sortable; // sort
    public function reverseSortValues(int $flags=\SORT_REGULAR): Sortable; // rsort
    public function sortValuesBy(callable $callback): Sortable; // usort

    public function sortKeys(int $flags=\SORT_REGULAR): Sortable; // ksort
    public function reverseSortKeys(int $flags=\SORT_REGULAR): Sortable; // krsort
    public function sortKeysBy(callable $callback): Sortable; // uksort

    public function reverse(): Sortable; // array_reverse
    public function reverseValues(): Sortable; // array_reverse
    public function shuffle(): Sortable; // kshuffle
    public function shuffleValues(): Sortable; // shuffle
}
