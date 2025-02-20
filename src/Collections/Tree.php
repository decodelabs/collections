<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use ArrayAccess;
use ArrayIterator;
use DecodeLabs\Coercion;
use DecodeLabs\Exceptional;
use DecodeLabs\Lucid\Provider\MixedContextTrait as SanitizerProviderTrait;
use Iterator;
use IteratorAggregate;

/**
 * @phpstan-import-type ChildList from TreeInterface
 * @template TValue of string|bool|int|float|resource|object
 * @implements TreeInterface<TValue>
 * @implements ArrayAccess<int|string,?TValue>
 * @implements IteratorAggregate<int|string,static>
 */
class Tree implements
    ArrayAccess,
    IteratorAggregate,
    TreeInterface
{
    /**
     * @use DictionaryTrait<TValue>
     */
    use DictionaryTrait;

    /**
     * @use SanitizerProviderTrait<TValue>
     */
    use SanitizerProviderTrait;

    protected const bool Mutable = true;

    /** @var non-empty-string */
    protected const string KeySeparator = '.';

    /**
     * @var ?TValue
     */
    protected mixed $value = null;

    /**
     * @var array<int|string,static>
     */
    protected array $items = [];

    /**
     * Value based construct
     */
    public function __construct(
        ?iterable $items = null,
        mixed $value = null
    ) {
        if (!is_iterable($value)) {
            $this->value = $value;
        }

        if ($items !== null) {
            $this->merge($items);
        }

        if (is_iterable($value)) {
            // @phpstan-ignore-next-line PHPStan bug
            $this->merge($value);
        }
    }


    /**
     * Clone whole tree
     */
    public function __clone(): void
    {
        foreach ($this->items as $key => $child) {
            $this->items[$key] = clone $child;
        }
    }



    /**
     * Set node value
     *
     * @param int|string $key
     */
    public function __set(
        int|string $key,
        mixed $value
    ): void {
        $this->items[$key] = new static(null, $value);
    }

    /**
     * Get node
     *
     * @param int|string $key
     * @return static
     */
    public function __get(
        int|string $key
    ): static {
        if (!array_key_exists($key, $this->items)) {
            $this->items[$key] = new static();
        }

        return $this->items[$key];
    }

    /**
     * Check for node
     *
     * @param int|string $key
     */
    public function __isset(
        int|string $key
    ): bool {
        return array_key_exists($key, $this->items);
    }

    /**
     * Remove node
     *
     * @param int|string $key
     */
    public function __unset(
        int|string $key
    ): void {
        unset($this->items[$key]);
    }



    /**
     * Set value by dot access
     *
     * @param int|string $key
     * @param TValue|iterable<int|string,TValue|iterable<int|string,static>>|null $value
     */
    public function setNode(
        int|string $key,
        mixed $value
    ): static {
        $node = $this->getNode($key);

        if (is_iterable($value)) {
            /** @var iterable<int|string,TValue> $value */
            $node->clear()->merge($value);
        } else {
            $node->setValue($value);
        }

        return $this;
    }

    /**
     * Get node by dot access
     *
     * @param int|string $key
     */
    public function getNode(
        int|string $key
    ): static {
        if (empty(static::KeySeparator)) {
            return $this->__get($key);
        }

        $node = $this;
        $parts = $this->splitNodeKey($key);

        foreach ($parts as $part) {
            $node = $node->__get($part);
        }

        return $node;
    }

    /**
     * True if any provided keys exist as a node
     *
     * @param int|string ...$keys
     */
    public function hasNode(
        int|string ...$keys
    ): bool {
        if (empty(static::KeySeparator)) {
            foreach ($keys as $key) {
                if (isset($this->items[$key])) {
                    return true;
                }
            }
        } else {
            foreach ($keys as $key) {
                $parts = $this->splitNodeKey($key);
                $node = $this;

                foreach ($parts as $part) {
                    if (!$node->__isset($part)) {
                        continue 2;
                    }

                    $node = $node->__get($part);
                }

                return true;
            }
        }

        return false;
    }

    /**
     * True if all provided keys exist as a node
     *
     * @param int|string ...$keys
     */
    public function hasAllNodes(
        int|string ...$keys
    ): bool {
        if (empty(static::KeySeparator)) {
            foreach ($keys as $key) {
                if (!isset($this->items[$key])) {
                    return false;
                }
            }
        } else {
            foreach ($keys as $key) {
                $parts = $this->splitNodeKey($key);
                $node = $this;

                foreach ($parts as $part) {
                    if (!$node->__isset($part)) {
                        return false;
                    }

                    $node = $node->__get($part);
                }
            }
        }

        return true;
    }


    /**
     * Split node key string
     *
     * @param int|string $key
     * @return list<int|string>
     */
    protected function splitNodeKey(
        int|string $key
    ): array {
        if (is_string($key)) {
            return explode(static::KeySeparator, $key);
        } else {
            return [$key];
        }
    }


    /**
     * Get first item, matching filter
     */
    public function getFirst(
        ?callable $filter = null
    ): mixed {
        return ArrayUtils::getFirst($this->items, $filter, $this)?->getValue();
    }

    /**
     * Get the last item in the list, matching filter
     */
    public function getLast(
        ?callable $filter = null
    ): mixed {
        return ArrayUtils::getLast($this->items, $filter, $this)?->getValue();
    }



    /**
     * Get value
     *
     * @param int|string $key
     */
    public function get(
        mixed $key
    ): mixed {
        return $this->getNode($key)->getValue();
    }

    /**
     * Retrieve entry and remove from collection
     *
     * @param int|string $key
     * @return TValue|null
     */
    public function pull(
        mixed $key
    ): mixed {
        $node = $this->getNode($key);
        $output = $node->pullValue();

        if ($node->isEmpty()) {
            unset($this[$key]);
        }

        return $output;
    }

    /**
     * Set value on node
     *
     * @param int|string $key
     */
    public function set(
        mixed $key,
        mixed $value
    ): static {
        $this->getNode($key)->setValue($value);
        return $this;
    }

    /**
     * True if any provided keys have a set value (not null)
     *
     * @param int|string ...$keys
     */
    public function has(
        mixed ...$keys
    ): bool {
        if (empty(static::KeySeparator)) {
            foreach ($keys as $key) {
                if (
                    isset($this->items[$key]) &&
                    $this->items[$key]->hasValue()
                ) {
                    return true;
                }
            }
        } else {
            foreach ($keys as $key) {
                $parts = $this->splitNodeKey($key);
                $node = $this;

                foreach ($parts as $part) {
                    if (!$node->__isset($part)) {
                        continue 2;
                    }

                    $node = $node->__get($part);
                }

                if ($node->hasValue()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * True if all provided keys have a set value (not null)
     *
     * @param int|string ...$keys
     */
    public function hasAll(
        mixed ...$keys
    ): bool {
        if (empty(static::KeySeparator)) {
            foreach ($keys as $key) {
                if (!(
                    isset($this->items[$key]) &&
                    $this->items[$key]->hasValue()
                )) {
                    return false;
                }
            }
        } else {
            foreach ($keys as $key) {
                $parts = $this->splitNodeKey($key);
                $node = $this;

                foreach ($parts as $part) {
                    if (!$node->__isset($part)) {
                        return false;
                    }

                    $node = $node->__get($part);
                }

                if (!$node->hasValue()) {
                    return false;
                }
            }
        }

        return true;
    }


    /**
     * Pull first item
     *
     * @return ?TValue
     */
    public function pop(): mixed
    {
        $node = array_pop($this->items);

        if ($node === null) {
            return null;
        }

        return $node->getValue();
    }

    /**
     * Pull last item
     *
     * @return ?TValue
     */
    public function shift(): mixed
    {
        $node = array_shift($this->items);

        if ($node === null) {
            return null;
        }

        return $node->getValue();
    }


    /**
     * Remove empty nodes
     */
    public function removeEmpty(): static
    {
        foreach ($this->items as $key => $node) {
            $node->removeEmpty();

            if (
                $node->isEmpty() &&
                !$node->hasValue()
            ) {
                unset($this->items[$key]);
            }
        }

        return $this;
    }

    /**
     * Lookup a key by value
     *
     * @return int|string|null
     */
    public function findKey(
        mixed $value,
        bool $strict = false
    ): mixed {
        foreach ($this->items as $key => $node) {
            if ($node->isValue($value, $strict)) {
                return $key;
            }
        }

        return null;
    }


    /**
     * Reset all values
     */
    public function clear(): static
    {
        $this->value = null;
        $this->items = [];
        return $this;
    }



    /**
     * Set by array access
     *
     * @param int|string|null $key
     * @param TValue|iterable<int|string,TValue|iterable<int|string,static>>|null $value
     */
    public function offsetSet(
        mixed $key,
        mixed $value
    ): void {
        if ($key === null) {
            $this->items[] = new static(null, $value);
        } elseif (is_iterable($value)) {
            /** @var iterable<int|string,TValue> $value */
            $this->getNode($key)->merge($value);
        } else {
            $this->getNode($key)->setValue($value);
        }
    }

    /**
     * Get by array access
     *
     * @param int|string $key
     */
    public function offsetGet(
        mixed $key
    ): mixed {
        return $this->getNode($key)->getValue();
    }

    /**
     * Check by array access
     *
     * @param int|string $key
     */
    public function offsetExists(
        mixed $key
    ): bool {
        if (!$this->hasNode($key)) {
            return false;
        }

        return $this->getNode($key)->hasValue();
    }





    /**
     * Set container value
     *
     * @param ?TValue $value
     */
    public function setValue(
        mixed $value
    ): static {
        if (is_iterable($value)) {
            /**
             * @var ChildList $value
             * @phpstan-ignore-next-line PHPStan bug
             */
            return $this->merge($value);
        }

        $this->value = $value;
        return $this;
    }

    /**
     * Get container value
     *
     * @return ?TValue
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get container value and remove
     */
    public function pullValue(): mixed
    {
        $output = $this->value;
        $this->value = null;
        return $output;
    }

    /**
     * Check container value
     */
    public function hasValue(): bool
    {
        return $this->value !== null;
    }

    /**
     * Check container and children for value
     */
    public function hasAnyValue(): bool
    {
        if ($this->hasValue()) {
            return true;
        }

        foreach ($this->items as $child) {
            if ($child->hasAnyValue()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compare value
     */
    public function isValue(
        mixed $value,
        bool $strict
    ): bool {
        if ($strict) {
            return $value === $this->value;
        } else {
            return $value == $this->value;
        }
    }


    /**
     * Return indexed sum list - filters non scalar first
     *
     * @return MapInterface<TValue,int>
     */
    public function countValues(): MapInterface
    {
        $output = array_count_values(
            array_map(function ($value) {
                $value = $value->getValue();

                if (
                    is_string($value) ||
                    is_int($value)
                ) {
                    return $value;
                } else {
                    return null;
                }
            }, $this->items)
        );

        /** @var MapInterface<TValue,int> */
        $output = new Dictionary($output);
        return $output;
    }


    /**
     * Return subset of collection where callback returns true
     */
    public function filter(
        ?callable $callback = null
    ): static {
        if ($callback) {
            $output = array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);
        } else {
            $output = array_filter($this->items, fn($node) => (bool)$node->getValue());
        }

        return $this->propagate($output);
    }



    /**
     * Convert to string
     */
    public function __toString(): string
    {
        return Coercion::asString($this->value);
    }


    /**
     * From query string
     *
     * @return self<string|bool>
     */
    public static function fromDelimitedString(
        string $string,
        string $setDelimiter = '&',
        string $valueDelimiter = '='
    ): self {
        if (
            $setDelimiter === '' ||
            $valueDelimiter === ''
        ) {
            throw Exceptional::UnexpectedValue(
                message: 'Cannot parse delimited string with empty delimiter'
            );
        }

        $output = new static();
        $string = trim($string);

        if (empty($string)) {
            return $output;
        }

        $parts = (array)explode($setDelimiter, $string);

        foreach ($parts as $part) {
            $valueParts = (array)explode($valueDelimiter, trim((string)$part), 2);
            $key = str_replace(['[', ']'], ['.', ''], urldecode((string)array_shift($valueParts)));

            if (count($valueParts) === 0) {
                $value = true;
            } else {
                $value = array_shift($valueParts);

                if (empty($value)) {
                    $value = null;
                }

                if ($value !== null) {
                    $value = urldecode($value);
                }
            }

            $output->setNode($key, $value);
        }

        return $output;
    }


    /**
     * To query string
     */
    public function toDelimitedString(
        string $setDelimiter = '&',
        string $valueDelimiter = '='
    ): string {
        $output = [];

        foreach ($this->toDelimitedSet(true) as $key => $value) {
            $key = rawurlencode((string)$key);

            if (
                $value !== true &&
                (
                    !empty($value) ||
                    $value === '0' ||
                    $value === 0
                )
            ) {
                $output[] = $key . $valueDelimiter . rawurlencode(
                    Coercion::asString($value)
                );
            } else {
                $output[] = $key;
            }
        }

        return implode($setDelimiter, $output);
    }

    /**
     * Convert to delimited set
     */
    public function toDelimitedSet(
        bool $urlEncode = false,
        ?string $prefix = null
    ): array {
        $output = [];

        if (
            $prefix !== null &&
            (
                $this->value !== null ||
                empty($this->items)
            )
        ) {
            $output[$prefix] = $this->getValue();
        }

        foreach ($this->items as $key => $child) {
            if ($urlEncode) {
                $key = rawurlencode((string)$key);
            }

            if ($prefix !== null) {
                $key = $prefix . '[' . $key . ']';
            }

            $output += $child->toDelimitedSet($urlEncode, (string)$key);
        }

        return $output;
    }


    /**
     * Map $values to values of collection as keys
     */
    public function combineWithValues(
        iterable $values
    ): static {
        $items = array_filter(
            array_map(function ($node) {
                $output = $node->getValue();

                if(is_int($output)) {
                    return $output;
                } else {
                    return Coercion::tryString($output);
                }
            }, $this->items),
            function ($value) {
                return $value !== null;
            }
        );

        /** @var array<int|string> $items */
        $result = array_combine($items, ArrayUtils::iterableToArray($values));

        // @phpstan-ignore-next-line PHPStan bug
        if($result === false) {
            throw Exceptional::InvalidArgument(
                'Key count does not match value count'
            );
        }

        return $this;
    }



    /**
     * Replace all values with $value
     */
    public function fill(
        mixed $value
    ): static {
        $result = array_fill_keys(array_keys($this->items), $value);
        return $this->clear()->merge($result);
    }


    /**
     * Flip keys and values
     *
     * @return MapInterface<int|string,int|string,int|string,int|string>
     * @phpstan-ignore-next-line
     */
    public function flip(): MapInterface
    {
        /** @var array<int|string,int|string> $items */
        $items = array_map(function ($node) {
            $output = $node->getValue();

            if(is_int($output)) {
                return $output;
            } else {
                return Coercion::toString($output);
            }
        }, $this->items);

        /** @var MapInterface<int|string,int|string,int|string,int|string> */
        $output = new Dictionary(array_flip($items));
        return $output;
    }



    /**
     * Merge all passed collections into one
     */
    public function merge(
        iterable ...$arrays
    ): static {
        foreach ($arrays as $array) {
            if ($array instanceof Tree) {
                /** @var TValue|null $value */
                $value = $array->getValue();
                $this->value = $value;

                foreach ($array->getChildren() as $key => $node) {
                    if (isset($this->items[$key])) {
                        $this->items[$key]->merge($node);
                    } else {
                        $this->items[$key] = new static($node);
                    }
                }
            } else {
                foreach ($array as $key => $value) {
                    if (isset($this->items[$key])) {
                        if (is_iterable($value)) {
                            // @phpstan-ignore-next-line PHPStan bug
                            $this->items[$key]->merge($value);
                        } else {
                            $this->items[$key]->setValue($value);
                        }
                    } else {
                        // @phpstan-ignore-next-line PHPStan bug
                        $this->items[$key] = new static(null, $value);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Merge EVERYTHING :D
     */
    public function mergeRecursive(
        iterable ...$arrays
    ): static {
        return $this->merge(...$arrays);
    }


    /**
     * Like merge, but replaces.. obvs
     */
    public function replace(
        iterable ...$arrays
    ): static {
        foreach ($arrays as $array) {
            if ($array instanceof Tree) {
                /** @var TValue|null $value */
                $value = $array->getValue();
                $this->value = $value;

                foreach ($array->getChildren() as $key => $node) {
                    $this->items[$key] = new static($node);
                }
            } else {
                foreach ($array as $key => $value) {
                    $this->items[$key] = new static(null, $value);
                }
            }
        }

        return $this;
    }

    /**
     * Alias of replace
     */
    public function replaceRecursive(
        iterable ...$arrays
    ): static {
        return $this->replace(...$arrays);
    }


    /**
     * Remove duplicates from collection
     */
    public function unique(
        int $flags = SORT_STRING
    ): static {
        $items = array_map(function ($node) {
            return Coercion::asString($node->getValue());
        }, $this->items);

        $items = array_unique($items, $flags);
        return $this->keep(...array_map('strval', array_keys($items)));
    }


    /**
     * @return array<int|string,TValue|array<mixed>|null>
     */
    public function getChildValues(): array
    {
        $output = [];

        foreach ($this->items as $key => $child) {
            if ($child->hasValue()) {
                $output[$key] = $child->getValue();
            } elseif (!$child->isEmpty()) {
                $output[$key] = $child->getChildValues();
            } else {
                $output[$key] = null;
            }
        }

        return $output;
    }

    /**
     * Recursive array conversion
     *
     * @return array<int|string,TValue|array<mixed>|null>
     */
    public function toArray(): array
    {
        $output = [];

        foreach ($this->items as $key => $child) {
            if (!$child->isEmpty()) {
                $output[$key] = $child->toArray();
            } else {
                $output[$key] = $child->getValue();
            }
        }

        return $output;
    }

    /**
     * Get just item array
     */
    public function getChildren(): array
    {
        return $this->items;
    }





    /**
     * Sort values, keep keys
     */
    public function sort(
        int $flags = \SORT_REGULAR
    ): static {
        uasort($this->items, function($a, $b) {
            return $a->value <=> $b->value;
        });

        return $this;
    }

    /**
     * Reverse sort values, keep keys
     */
    public function reverseSort(
        int $flags = \SORT_REGULAR
    ): static {
        uasort($this->items, function($a, $b) {
            return $b->value <=> $a->value;
        });

        return $this;
    }

    /**
     * Sort values, ignore keys
     */
    public function sortValues(
        int $flags = \SORT_REGULAR
    ): static {
        usort($this->items, function($a, $b) {
            return $a->value <=> $b->value;
        });

        return $this;
    }

    /**
     * Reverse sort values, ignore keys
     */
    public function reverseSortValues(
        int $flags = \SORT_REGULAR
    ): static {
        usort($this->items, function($a, $b) {
            return $b->value <=> $a->value;
        });

        return $this;
    }






    /**
     * Iterator interface
     *
     * @return ArrayIterator<int|string,static>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }



    /**
     * Get dump info
     *
     * @return array<mixed>
     */
    public function __debugInfo(): array
    {
        $output = [];

        foreach ($this->items as $key => $child) {
            if (empty($child->items)) {
                $output[$key] = $child->value;
            } else {
                $output[$key] = $child;
            }
        }

        if (empty($output)) {
            if ($this->value !== null) {
                return [
                    '⇒ value' => $this->value // @ignore-non-ascii
                ];
            } else {
                return [];
            }
        }

        if ($this->value !== null) {
            $output = [
                '⇒ value' => $this->value, // @ignore-non-ascii
                '⇒ children' => $output // @ignore-non-ascii
            ];
        }

        return $output;
    }



    /**
     * Copy and reinitialise new object
     *
     * @param iterable<int|string,TValue|iterable<mixed>> $newItems
     * @param TValue|null $value
     */
    protected static function propagate(
        ?iterable $newItems = [],
        mixed $value = null
    ): static {
        /** @phpstan-ignore-next-line */
        return new static($newItems, $value);
    }
}
