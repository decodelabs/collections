<?php
/**
 * This file is part of the Collections package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Collections\Native;

use DecodeLabs\Collections\ArrayUtils;
use DecodeLabs\Collections\Sortable;

trait SortableTrait
{
    /**
     * Sort values, keep keys
     */
    public function sort(int $flags=\SORT_REGULAR): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        asort($output->items, $flags);
        return $output;
    }

    /**
     * Reverse sort values, keep keys
     */
    public function reverseSort(int $flags=\SORT_REGULAR): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        arsort($output->items, $flags);
        return $output;
    }

    /**
     * Sort values using callback, keep keys
     */
    public function sortBy(callable $callable): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        uasort($output->items, $callable);
        return $output;
    }


    /**
     * Natural sort values, keep keys
     */
    public function sortNatural(): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        natsort($output->items);
        return $output;
    }

    /**
     * Natural sort values, case insensitive, keep keys
     */
    public function sortCaseNatural(): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        natcasesort($output->items);
        return $output;
    }


    /**
     * Sort values, ignore keys
     */
    public function sortValues(int $flags=\SORT_REGULAR): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        sort($output->items, $flags);
        return $output;
    }

    /**
     * Reverse sort values, ignore keys
     */
    public function reverseSortValues(int $flags=\SORT_REGULAR): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        rsort($output->items, $flags);
        return $output;
    }

    /**
     * Sort values by callback, ignore keys
     */
    public function sortValuesBy(callable $callback): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        usort($output->items, $callback);
        return $output;
    }


    /**
     * Sort values by key
     */
    public function sortKeys(int $flags=\SORT_REGULAR): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        ksort($output->items, $flags);
        return $output;
    }

    /**
     * Reverse sort values by key
     */
    public function reverseSortKeys(int $flags=\SORT_REGULAR): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        krsort($output->items, $flags);
        return $output;
    }

    /**
     * Sort values by key using callback
     */
    public function sortKeysBy(callable $callback): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        uksort($output->items, $callback);
        return $output;
    }


    /**
     * Reverse all entries
     */
    public function reverse(): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        $output->items = array_reverse($output->items, true);
        return $output;
    }

    /**
     * Reverse all entries, ignore keys
     */
    public function reverseValues(): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        $output->items = array_reverse($output->items, false);
        return $output;
    }

    /**
     * Randomise order, keep keys
     */
    public function shuffle(): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        $output->items = ArrayUtils::kshuffle($output->items);
        return $output;
    }

    /**
     * Randomise order, ignore keys
     */
    public function shuffleValues(): Sortable
    {
        $output = static::MUTABLE ? $this : $this->copy();
        shuffle($output->items);
        return $output;
    }
}
