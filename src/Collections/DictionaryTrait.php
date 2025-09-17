<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use DecodeLabs\Exceptional;

/**
 * @template TValue
 * @phpstan-require-implements MapInterface<int|string,TValue>
 */
trait DictionaryTrait
{
    /**
     * @use CollectionTrait<int|string,TValue,TValue>
     */
    use CollectionTrait;
    use SortableTrait;

    /**
     * @return list<int|string>
     */
    public function getKeys(): array
    {
        return array_keys($this->items);
    }


    /**
     * @param int|string $key
     */
    public function get(
        mixed $key
    ): mixed {
        return $this->items[$key] ?? null;
    }

    /**
     * @param int|string $key
     */
    public function pull(
        mixed $key
    ): mixed {
        $output = $this->items[$key] ?? null;

        if (static::Mutable) {
            unset($this->items[$key]);
        }

        return $output;
    }

    /**
     * @param int|string $key
     */
    public function set(
        mixed $key,
        mixed $value
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items[$key] = $value;
        return $output;
    }

    /**
     * @param int|string ...$keys
     */
    public function has(
        mixed ...$keys
    ): bool {
        foreach ($keys as $key) {
            if (isset($this->items[$key])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int|string ...$keys
     */
    public function hasAll(
        mixed ...$keys
    ): bool {
        foreach ($keys as $key) {
            if (!isset($this->items[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int|string ...$keys
     */
    public function hasKey(
        mixed ...$keys
    ): bool {
        foreach ($keys as $key) {
            if (array_key_exists($key, $this->items)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int|string ...$keys
     */
    public function hasKeys(
        mixed ...$keys
    ): bool {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $this->items)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int|string ...$keys
     */
    public function remove(
        mixed ...$keys
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_diff_key($output->items, array_flip($keys));
        return $output;
    }

    /**
     * @param int|string ...$keys
     */
    public function keep(
        mixed ...$keys
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_intersect_key($output->items, array_flip($keys));
        return $output;
    }


    /**
     * @return int|string|null
     */
    public function findKey(
        mixed $value,
        bool $strict = false
    ): mixed {
        if (false === ($key = array_search($value, $this->items, $strict))) {
            return null;
        }

        return $key;
    }


    public function clear(): static
    {
        $output = static::Mutable ? $this : clone $this;
        $output->items = [];
        return $output;
    }

    public function clearKeys(): static
    {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_values($output->items);
        return $output;
    }


    public function collapse(
        bool $unique = false,
        bool $removeNull = false
    ): static {
        $output = static::Mutable ? $this : clone $this;
        // @phpstan-ignore-next-line PHPStan bug
        $output->items = ArrayUtils::collapse($output->items, true, $unique, $removeNull);
        return $output;
    }

    public function collapseValues(
        bool $unique = false,
        bool $removeNull = false
    ): static {
        $output = static::Mutable ? $this : clone $this;
        // @phpstan-ignore-next-line PHPStan bug
        $output->items = ArrayUtils::collapse($output->items, false, $unique, $removeNull);
        return $output;
    }



    public function changeKeyCase(
        int $case = CASE_LOWER
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_change_key_case($output->items, $case);
        return $output;
    }


    /**
     * @param iterable<int|string> $keys
     */
    public function combineWithKeys(
        iterable $keys
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $result = array_combine(ArrayUtils::iterableToArray($keys), $output->items);

        // @phpstan-ignore-next-line PHPStan bug
        if ($result === false) {
            throw Exceptional::InvalidArgument(
                'Key count does not match value count'
            );
        }

        // @phpstan-ignore-next-line PHPStan bug
        $output->items = $result;
        return $output;
    }

    /**
     * @param iterable<TValue> $values
     */
    public function combineWithValues(
        iterable $values
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $result = array_combine(
            array_filter($output->items, fn ($value) => is_string($value) || is_int($value)),
            ArrayUtils::iterableToArray($values)
        );

        // @phpstan-ignore-next-line PHPStan bug
        if ($result === false) {
            throw Exceptional::InvalidArgument(
                'Key count does not match value count'
            );
        }

        return $output;
    }


    /**
     * @param TValue $value
     */
    public function fill(
        mixed $value
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_fill_keys(array_keys($output->items), $value);
        return $output;
    }

    public function flip(): MapInterface
    {
        $output = clone $this;

        /** @var array<TValue> $items */
        $items = array_flip(
            array_filter($output->items, fn ($value) => is_string($value) || is_int($value))
        );

        $output->items = $items;

        return $output;
    }


    /**
     * @param iterable<int|string,TValue> ...$arrays
     */
    public function merge(
        iterable ...$arrays
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_merge($output->items, ...ArrayUtils::iterablesToArrays(...$arrays));
        return $output;
    }

    /**
     * @param iterable<int|string,TValue> ...$arrays
     */
    public function mergeRecursive(
        iterable ...$arrays
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_merge_recursive($output->items, ...ArrayUtils::iterablesToArrays(...$arrays));
        return $output;
    }


    /**
     * @param iterable<int|string,TValue> ...$arrays
     */
    public function replace(
        iterable ...$arrays
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_replace($output->items, ...ArrayUtils::iterablesToArrays(...$arrays));
        return $output;
    }

    /**
     * @param iterable<int|string,TValue> ...$arrays
     */
    public function replaceRecursive(
        iterable ...$arrays
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_replace_recursive($output->items, ...ArrayUtils::iterablesToArrays(...$arrays));
        return $output;
    }



    /**
     * @param-out MapInterface<int|string,TValue> $removed
     */
    public function removeSlice(
        int $offset,
        ?int $length = null,
        ?MapInterface &$removed = null
    ): static {
        $output = static::Mutable ? $this : clone $this;

        if ($length === null) {
            $length = count($output->items);
        }

        $removed = $this->propagate(
            array_splice($output->items, $offset, $length)
        );

        return $output;
    }

    /**
     * @param iterable<int|string,TValue> $replacement
     * @param-out MapInterface<int|string,TValue> $removed
     */
    public function replaceSlice(
        int $offset,
        ?int $length,
        iterable $replacement,
        ?MapInterface &$removed = null
    ): static {
        $output = static::Mutable ? $this : clone $this;

        if ($length === null) {
            $length = count($output->items);
        }

        $removed = $this->propagate(
            // @phpstan-ignore-next-line
            array_splice($output->items, $offset, $length, ArrayUtils::iterableToArray($replacement))
        );

        return $output;
    }


    public function unique(
        int $flags = SORT_STRING
    ): static {
        $output = static::Mutable ? $this : clone $this;
        $output->items = array_unique($output->items, $flags);
        return $output;
    }


    public function walk(
        callable $callback,
        mixed $data = null
    ): static {
        $output = static::Mutable ? $this : clone $this;
        array_walk($output->items, $callback, $data);
        return $output;
    }

    public function walkRecursive(
        callable $callback,
        mixed $data = null
    ): static {
        $output = static::Mutable ? $this : clone $this;
        array_walk_recursive($output->items, $callback, $data);
        return $output;
    }




    /**
     * @template FValue
     * @param iterable<int|string, FValue> $newItems
     */
    protected static function propagate(
        iterable $newItems = []
    ): static {
        /** @var static $output */
        $output = new self($newItems);

        return $output;
    }
}
