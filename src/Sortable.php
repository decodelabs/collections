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
    public function sort(
        int $flags = \SORT_REGULAR
    ): static; // asort

    /**
     * @return static
     */
    public function reverseSort(
        int $flags = \SORT_REGULAR
    ): static; // arsort

    /**
     * @return static
     */
    public function sortBy(
        callable $callable
    ): static; // uasort


    /**
     * @return static
     */
    public function sortNatural(): static; // natsort

    /**
     * @return static
     */
    public function sortCaseNatural(): static; // natcasesort


    /**
     * @return static
     */
    public function sortValues(
        int $flags = \SORT_REGULAR
    ): static; // sort

    /**
     * @return static
     */
    public function reverseSortValues(
        int $flags = \SORT_REGULAR
    ): static; // rsort

    /**
     * @return static
     */
    public function sortValuesBy(
        callable $callback
    ): static; // usort


    /**
     * @return static
     */
    public function sortKeys(
        int $flags = \SORT_REGULAR
    ): static; // ksort

    /**
     * @return static
     */
    public function reverseSortKeys(
        int $flags = \SORT_REGULAR
    ): static; // krsort

    /**
     * @return static
     */
    public function sortKeysBy(
        callable $callback
    ): static; // uksort


    /**
     * @return static
     */
    public function reverse(): static; // array_reverse

    /**
     * @return static
     */
    public function reverseValues(): static; // array_reverse

    /**
     * @return static
     */
    public function shuffle(): static; // kshuffle

    /**
     * @return static
     */
    public function shuffleValues(): static; // shuffle
}
