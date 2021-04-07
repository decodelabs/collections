<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

interface Sortable
{
    /**
     * @return static
     */
    public function sort(int $flags = \SORT_REGULAR): Sortable; // asort

    /**
     * @return static
     */
    public function reverseSort(int $flags = \SORT_REGULAR): Sortable; // arsort

    /**
     * @return static
     */
    public function sortBy(callable $callable): Sortable; // uasort


    /**
     * @return static
     */
    public function sortNatural(): Sortable; // natsort

    /**
     * @return static
     */
    public function sortCaseNatural(): Sortable; // natcasesort


    /**
     * @return static
     */
    public function sortValues(int $flags = \SORT_REGULAR): Sortable; // sort

    /**
     * @return static
     */
    public function reverseSortValues(int $flags = \SORT_REGULAR): Sortable; // rsort

    /**
     * @return static
     */
    public function sortValuesBy(callable $callback): Sortable; // usort


    /**
     * @return static
     */
    public function sortKeys(int $flags = \SORT_REGULAR): Sortable; // ksort

    /**
     * @return static
     */
    public function reverseSortKeys(int $flags = \SORT_REGULAR): Sortable; // krsort

    /**
     * @return static
     */
    public function sortKeysBy(callable $callback): Sortable; // uksort


    /**
     * @return static
     */
    public function reverse(): Sortable; // array_reverse

    /**
     * @return static
     */
    public function reverseValues(): Sortable; // array_reverse

    /**
     * @return static
     */
    public function shuffle(): Sortable; // kshuffle

    /**
     * @return static
     */
    public function shuffleValues(): Sortable; // shuffle
}
