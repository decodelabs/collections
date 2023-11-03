<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Native;

use DecodeLabs\Collections\ArrayUtils;

trait SortableTrait
{
    /**
     * Sort values, keep keys
     */
    public function sort(
        int $flags = \SORT_REGULAR
    ): static {
        $output = static::MUTABLE ? $this : clone $this;
        asort($output->items, $flags);
        return $output;
    }

    /**
     * Reverse sort values, keep keys
     */
    public function reverseSort(
        int $flags = \SORT_REGULAR
    ): static {
        $output = static::MUTABLE ? $this : clone $this;
        arsort($output->items, $flags);
        return $output;
    }

    /**
     * Sort values using callback, keep keys
     */
    public function sortBy(
        callable $callable
    ): static {
        $output = static::MUTABLE ? $this : clone $this;
        uasort($output->items, $callable);
        return $output;
    }


    /**
     * Natural sort values, keep keys
     */
    public function sortNatural(): static
    {
        $output = static::MUTABLE ? $this : clone $this;
        natsort($output->items);
        return $output;
    }

    /**
     * Natural sort values, case insensitive, keep keys
     */
    public function sortCaseNatural(): static
    {
        $output = static::MUTABLE ? $this : clone $this;
        natcasesort($output->items);
        return $output;
    }


    /**
     * Sort values, ignore keys
     */
    public function sortValues(
        int $flags = \SORT_REGULAR
    ): static {
        $output = static::MUTABLE ? $this : clone $this;
        sort($output->items, $flags);
        return $output;
    }

    /**
     * Reverse sort values, ignore keys
     */
    public function reverseSortValues(
        int $flags = \SORT_REGULAR
    ): static {
        $output = static::MUTABLE ? $this : clone $this;
        rsort($output->items, $flags);
        return $output;
    }

    /**
     * Sort values by callback, ignore keys
     */
    public function sortValuesBy(
        callable $callback
    ): static {
        $output = static::MUTABLE ? $this : clone $this;
        usort($output->items, $callback);
        return $output;
    }


    /**
     * Sort values by key
     */
    public function sortKeys(
        int $flags = \SORT_REGULAR
    ): static {
        $output = static::MUTABLE ? $this : clone $this;
        ksort($output->items, $flags);
        return $output;
    }

    /**
     * Reverse sort values by key
     */
    public function reverseSortKeys(
        int $flags = \SORT_REGULAR
    ): static {
        $output = static::MUTABLE ? $this : clone $this;
        krsort($output->items, $flags);
        return $output;
    }

    /**
     * Sort values by key using callback
     */
    public function sortKeysBy(
        callable $callback
    ): static {
        $output = static::MUTABLE ? $this : clone $this;
        uksort($output->items, $callback);
        return $output;
    }


    /**
     * Reverse all entries
     */
    public function reverse(): static
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_reverse($output->items, true);
        return $output;
    }

    /**
     * Reverse all entries, ignore keys
     */
    public function reverseValues(): static
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_reverse($output->items, false);
        return $output;
    }

    /**
     * Randomise order, keep keys
     */
    public function shuffle(): static
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = ArrayUtils::kshuffle($output->items);
        return $output;
    }

    /**
     * Randomise order, ignore keys
     */
    public function shuffleValues(): static
    {
        $output = static::MUTABLE ? $this : clone $this;
        shuffle($output->items);
        return $output;
    }
}
