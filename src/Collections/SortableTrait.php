<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

/**
 * @phpstan-require-implements Sortable
 */
trait SortableTrait
{
    public function sort(
        int $flags = \SORT_REGULAR
    ): static {
        $output = static::Mutable ? $this : clone $this;
        asort($output->items, $flags);
        return $output;
    }

    public function reverseSort(
        int $flags = \SORT_REGULAR
    ): static {
        $output = static::Mutable ? $this : clone $this;
        arsort($output->items, $flags);
        return $output;
    }

    public function sortBy(
        callable $callable
    ): static {
        $output = static::Mutable ? $this : clone $this;
        uasort($output->items, $callable);
        return $output;
    }


    public function sortNatural(): static
    {
        $output = static::Mutable ? $this : clone $this;
        natsort($output->items);
        return $output;
    }

    public function sortCaseNatural(): static
    {
        $output = static::Mutable ? $this : clone $this;
        natcasesort($output->items);
        return $output;
    }


    public function sortValues(
        int $flags = \SORT_REGULAR
    ): static {
        $output = static::Mutable ? $this : clone $this;
        sort($output->items, $flags);
        return $output;
    }

    public function reverseSortValues(
        int $flags = \SORT_REGULAR
    ): static {
        $output = static::Mutable ? $this : clone $this;
        rsort($output->items, $flags);
        return $output;
    }

    public function sortValuesBy(
        callable $callback
    ): static {
        $output = static::Mutable ? $this : clone $this;
        usort($output->items, $callback);
        return $output;
    }


    public function sortKeys(
        int $flags = \SORT_REGULAR
    ): static {
        $output = static::Mutable ? $this : clone $this;
        ksort($output->items, $flags);
        return $output;
    }

    public function reverseSortKeys(
        int $flags = \SORT_REGULAR
    ): static {
        $output = static::Mutable ? $this : clone $this;
        krsort($output->items, $flags);
        return $output;
    }

    public function sortKeysBy(
        callable $callback
    ): static {
        $output = static::Mutable ? $this : clone $this;
        uksort($output->items, $callback);
        return $output;
    }



    public function reverse(): static
    {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_reverse($output->items, true);
        return $output;
    }

    public function reverseValues(): static
    {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_reverse($output->items, false);
        return $output;
    }

    public function shuffle(): static
    {
        $output = static::Mutable ? $this : clone $this;
        // @phpstan-ignore-next-line
        ArrayUtils::kshuffle($output->items);
        return $output;
    }

    public function shuffleValues(): static
    {
        $output = static::Mutable ? $this : clone $this;
        shuffle($output->items);
        return $output;
    }
}
