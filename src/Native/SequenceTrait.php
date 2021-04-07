<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Native;

use DecodeLabs\Collections\ArrayUtils;
use DecodeLabs\Collections\Collection;
use DecodeLabs\Collections\Sequence;

use DecodeLabs\Exceptional;

/**
 * @template TValue
 */
trait SequenceTrait
{
    /**
     * @use CollectionTrait<int, TValue>
     */
    use CollectionTrait;
    use SortableTrait;

    /**
     * Direct set items
     *
     * @param iterable<TValue> $items
     */
    public function __construct(iterable $items)
    {
        $this->items = array_values(
            ArrayUtils::iterableToArray($items)
        );
    }


    /**
     * Get all keys in array, enforce int formatting
     */
    public function getKeys(): array
    {
        return array_map('intval', array_keys($this->items));
    }


    /**
     * Get item by index
     */
    public function get(int $key)
    {
        if ($key < 0) {
            $key += count($this->items);

            if ($key < 0) {
                throw Exceptional::OutOfBounds(
                    'Index ' . $key . ' is not accessible',
                    null,
                    $this
                );
            }
        }

        return $this->items[$key] ?? null;
    }

    /**
     * Get and remove item by index
     */
    public function pull(int $key)
    {
        $key = $this->normalizeKey($key);
        $output = $this->items[$key] ?? null;

        if (static::MUTABLE) {
            unset($this->items[$key]);
            $this->items = array_values($this->items);
        }

        return $output;
    }

    /**
     * Set a value by index, keys normalized
     */
    public function set(int $key, $value): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $count = count($output->items);
        $key = min($this->normalizeKey($key), $count);

        $output->items[$key] = $value;
        return $output;
    }

    /**
     * Add an item in at selected index, move rest
     */
    public function put(int $key, $value): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $count = count($output->items);
        $key = $this->normalizeKey($key);

        $addVals = null;

        if ($key < $count) {
            $addVals = array_splice($output->items, $key);
        }

        $output->items[] = $value;

        if ($addVals !== null) {
            $output->items = array_merge($output->items, $addVals);
        }

        return $output;
    }



    /**
     * True if any provided keys have a set value (not null)
     */
    public function has(int ...$keys): bool
    {
        $count = count($this->items);

        foreach ($keys as $key) {
            if ($key < 0) {
                $key += $count;

                if ($key < 0) {
                    throw Exceptional::OutOfBounds(
                        'Index ' . $key . ' is not accessible',
                        null,
                        $this
                    );
                }
            }

            if (isset($this->items[$key])) {
                return true;
            }
        }

        return false;
    }

    /**
     * True if all provided keys have a set value (not null)
     */
    public function hasAll(int ...$keys): bool
    {
        $count = count($this->items);

        foreach ($keys as $key) {
            if ($key < 0) {
                $key += $count;

                if ($key < 0) {
                    throw Exceptional::OutOfBounds(
                        'Index ' . $key . ' is not accessible',
                        null,
                        $this
                    );
                }
            }

            if (!isset($this->items[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * True if any provided keys are in the collection
     */
    public function hasKey(int ...$keys): bool
    {
        $count = count($this->items);

        foreach ($keys as $key) {
            if ($key < 0) {
                $key += $count;

                if ($key < 0) {
                    throw Exceptional::OutOfBounds(
                        'Index ' . $key . ' is not accessible',
                        null,
                        $this
                    );
                }
            }

            if (array_key_exists($key, $this->items)) {
                return true;
            }
        }

        return false;
    }

    /**
     * True if all provided keys are in the collection
     */
    public function hasKeys(int ...$keys): bool
    {
        $count = count($this->items);

        foreach ($keys as $key) {
            if ($key < 0) {
                $key += $count;

                if ($key < 0) {
                    throw Exceptional::OutOfBounds(
                        'Index ' . $key . ' is not accessible',
                        null,
                        $this
                    );
                }
            }

            if (!array_key_exists($key, $this->items)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove all values associated with $keys
     */
    public function remove(int ...$keys): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;

        array_walk($keys, function (&$key) {
            $key = $this->normalizeKey($key);
        });

        $output->items = array_values(array_diff_key($output->items, array_flip($keys)));
        return $output;
    }

    /**
     * Remove all values not associated with $keys
     */
    public function keep(int ...$keys): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;

        array_walk($keys, function (&$key) {
            $key = $this->normalizeKey($key);
        });

        $output->items = array_values(array_intersect_key($output->items, array_flip($keys)));
        return $output;
    }



    /**
     * Lookup a key by value
     */
    public function findKey($value, bool $strict = false): ?int
    {
        if (false === ($key = array_search($value, $this->items, $strict))) {
            return null;
        }

        return (int)$key;
    }



    /**
     * Reset all values
     */
    public function clear(): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = [];
        return $output;
    }

    /**
     * Remove all keys
     */
    public function clearKeys(): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_values($output->items);
        return $output;
    }



    /**
     * Collapse multi dimensional array to flat
     */
    public function collapse(bool $unique = false, bool $removeNull = false): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = ArrayUtils::collapse($output->items, false, $unique, $removeNull);
        return $output;
    }




    /**
     * Replace all values with $value
     */
    public function fill($value): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_fill_keys(array_keys($output->items), $value);
        return $output;
    }

    /**
     * Create a new sequence with numeric range
     */
    public static function createFill(int $length, $value): Sequence
    {
        return static::propagate(array_fill(0, $length, $value));
    }



    /**
     * Merge all passed collections into one
     */
    public function merge(iterable ...$arrays): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_values(array_merge($output->items, ...ArrayUtils::iterablesToArrays(...$arrays)));
        return $output;
    }

    /**
     * Merge EVERYTHING :D
     */
    public function mergeRecursive(iterable ...$arrays): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_values(array_merge_recursive($output->items, ...ArrayUtils::iterablesToArrays(...$arrays)));
        return $output;
    }


    /**
     * Like merge, but replaces.. obvs
     */
    public function replace(iterable ...$arrays): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_values(array_replace($output->items, ...ArrayUtils::iterablesToArrays(...$arrays)) ?? []);
        return $output;
    }

    /**
     * Replace EVERYTHING :D
     */
    public function replaceRecursive(iterable ...$arrays): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_values(array_replace_recursive($output->items, ...ArrayUtils::iterablesToArrays(...$arrays)) ?? []);
        return $output;
    }



    /**
     * Ensure sequence is at least $size long
     */
    public function padLeft(int $size, $value = null): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_pad($output->items, 0 - abs($size), $value);
        return $output;
    }

    /**
     * Ensure sequence is at least $size long
     */
    public function padRight(int $size, $value = null): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_pad($output->items, abs($size), $value);
        return $output;
    }

    /**
     * Ensure sequence is at least $size long
     */
    public function padBoth(int $size, $value = null): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $length = $output->count();

        if (($size = abs($size)) < $length) {
            return $output;
        }

        $padSize = ($size - $length) / 2;
        $leftSize = $length + floor($padSize);
        $rightSize = $size;

        $output->items = array_pad($output->items, (int)-$leftSize, $value);
        $output->items = array_pad($output->items, (int)$rightSize, $value);
        return $output;
    }



    /**
     * Remove $offet + $length items
     */
    public function removeSlice(int $offset, int $length = null, Sequence &$removed = null): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $count = count($output->items);
        $offset = $this->normalizeKey($offset);

        if ($length === null) {
            $length = $count;
        }

        $removed = $this->propagate(
            array_splice($output->items, $offset, $length)
        );

        return $output;
    }

    /**
     * Like removeSlice, but leaves a present behind
     */
    public function replaceSlice(int $offset, int $length = null, iterable $replacement, Sequence &$removed = null): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $count = count($output->items);
        $offset = $this->normalizeKey($offset);

        if ($length === null) {
            $length = $count;
        }

        $removed = $this->propagate(
            array_splice($output->items, $offset, $length, array_values(ArrayUtils::iterableToArray($replacement)))
        );

        return $output;
    }



    /**
     * Remove duplicates from collection
     */
    public function unique(int $flags = SORT_STRING): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        $output->items = array_unique($output->items, $flags);
        return $output;
    }


    /**
     * Iterate each entry
     */
    public function walk(callable $callback, $data = null): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        array_walk($output->items, $callback, $data);
        return $output;
    }

    /**
     * Iterate everything
     */
    public function walkRecursive(callable $callback, $data = null): Sequence
    {
        $output = static::MUTABLE ? $this : clone $this;
        array_walk_recursive($output->items, $callback, $data);
        return $output;
    }



    /**
     * Create a collection of numbers
     */
    public static function createRange(int $start, int $end, int $step = 1): Sequence
    {
        return static::propagate(range($start, $end, $step));
    }


    /**
     * Prepare an index
     */
    protected function normalizeKey(int $key): int
    {
        if ($key < 0) {
            $key += count($this->items);

            if ($key < 0) {
                throw Exceptional::OutOfBounds(
                    'Index ' . $key . ' is not accessible',
                    null,
                    $this
                );
            }
        }

        return $key;
    }



    /**
     * Copy and reinitialise new object
     *
     * @template FValue
     * @param iterable<FValue> $newItems
     * @return static<FValue>
     */
    protected static function propagate(iterable $newItems = []): Sequence
    {
        return new self($newItems);
    }
}
