<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Tree;

use ArrayIterator;

use DecodeLabs\Collections\ArrayUtils;
use DecodeLabs\Collections\Native\HashMapTrait;
use DecodeLabs\Collections\Tree;
use DecodeLabs\Exceptional;
use DecodeLabs\Gadgets\Sanitizer;

use Iterator;
use IteratorAggregate;

/**
 * @template TValue
 * @implements Tree<TValue>
 * @implements IteratorAggregate<int|string, static>
 */
class NativeMutable implements
    IteratorAggregate,
    Tree
{
    /**
     * @use HashMapTrait<TValue>
     */
    use HashMapTrait;

    public const MUTABLE = true;
    public const KEY_SEPARATOR = '.';

    /**
     * @phpstan-var TValue|null
     */
    protected mixed $value = null;

    /**
     * @phpstan-var array<int|string, static>
     */
    protected array $items = [];

    /**
     * Value based construct
     */
    public function __construct(
        iterable $items = null,
        mixed $value = null
    ) {
        if (!is_iterable($value)) {
            $this->value = $value;
        }

        if ($items !== null) {
            $this->merge($items);
        }

        if (is_iterable($value)) {
            /** @phpstan-var iterable<int|string, TValue|iterable<mixed>> $value */
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
     */
    public function __set(
        int|string $key,
        mixed $value
    ): void {
        $this->items[$key] = new static(null, $value);
    }

    /**
     * Get node
     */
    public function __get(int|string $key): static
    {
        if (!array_key_exists($key, $this->items)) {
            $this->items[$key] = new static();
        }

        return $this->items[$key];
    }

    /**
     * Check for node
     */
    public function __isset(int|string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Remove node
     */
    public function __unset(int|string $key): void
    {
        unset($this->items[$key]);
    }



    /**
     * Set value by dot access
     */
    public function setNode(
        int|string $key,
        mixed $value
    ): static {
        $node = $this->getNode($key);

        if (is_iterable($value)) {
            /** @phpstan-var iterable<int|string, TValue> $value */
            $node->clear()->merge($value);
        } else {
            $node->setValue($value);
        }

        return $this;
    }

    /**
     * Get node by dot access
     */
    public function getNode(int|string $key): static
    {
        if (empty(static::KEY_SEPARATOR)) {
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
     */
    public function hasNode(int|string ...$keys): bool
    {
        if (empty(static::KEY_SEPARATOR)) {
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
     */
    public function hasAllNodes(int|string ...$keys): bool
    {
        if (empty(static::KEY_SEPARATOR)) {
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
     * @return array<int|string>
     */
    protected function splitNodeKey(int|string $key): array
    {
        $parts = false;

        if (is_string($key)) {
            $parts = explode(static::KEY_SEPARATOR, $key);
        }

        if ($parts === false) {
            $parts = [$key];
        }

        return $parts;
    }




    /**
     * Get value
     */
    public function get(int|string $key): mixed
    {
        return $this->getNode($key)->getValue();
    }

    /**
     * Retrieve entry and remove from collection
     *
     * @phpstan-return TValue|null
     */
    public function pull(int|string $key): mixed
    {
        $node = $this->getNode($key);
        $output = $node->pullValue();

        if ($node->isEmpty()) {
            unset($this[$key]);
        }

        return $output;
    }

    /**
     * Set value on node
     */
    public function set(
        int|string $key,
        mixed $value
    ): static {
        $this->getNode($key)->setValue($value);
        return $this;
    }

    /**
     * True if any provided keys have a set value (not null)
     */
    public function has(int|string ...$keys): bool
    {
        if (empty(static::KEY_SEPARATOR)) {
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
     */
    public function hasAll(int|string ...$keys): bool
    {
        if (empty(static::KEY_SEPARATOR)) {
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

            if ($node->isEmpty() && !$node->hasValue()) {
                unset($this->items[$key]);
            }
        }

        return $this;
    }

    /**
     * Lookup a key by value
     */
    public function findKey(
        mixed $value,
        bool $strict = false
    ): ?string {
        foreach ($this->items as $key => $node) {
            if ($node->isValue($value, $strict)) {
                return (string)$key;
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
     * @phpstan-param TValue|iterable<int|string, TValue|iterable<mixed>>|null $value
     */
    public function offsetSet(
        mixed $key,
        mixed $value
    ): void {
        if ($key === null) {
            $this->items[] = new static(null, $value);
        } elseif (is_iterable($value)) {
            /** @phpstan-var iterable<int|string, TValue> $value */
            $this->getNode($key)->merge($value);
        } else {
            $this->getNode($key)->setValue($value);
        }
    }

    /**
     * Get by array access
     */
    public function offsetGet(mixed $key): mixed
    {
        return $this->getNode($key)->getValue();
    }

    /**
     * Check by array access
     */
    public function offsetExists(mixed $key): bool
    {
        return $this->getNode($key)->hasValue();
    }



    /**
     * Get node and return value sanitizer
     */
    public function sanitize(
        int|string $key,
        bool $required = true
    ): Sanitizer {
        return $this->getNode($key)->sanitizeValue($required);
    }

    /**
     * Get node and sanitize with custom sanitizer
     */
    public function sanitizeWith(
        int|string $key,
        callable $sanitizer,
        bool $required = true
    ): mixed {
        return $this->getNode($key)->sanitizeValue($required)->with($sanitizer);
    }

    /**
     * Return new Sanitizer with node value
     */
    public function sanitizeValue(bool $required = true): Sanitizer
    {
        return new Sanitizer($this->getValue(), $required);
    }

    /**
     * Sanitize value with custom sanitizer
     */
    public function sanitizeValueWith(
        callable $sanitizer,
        bool $required = true
    ): mixed {
        return $this->sanitizeValue($required)->with($sanitizer);
    }




    /**
     * Set container value
     */
    public function setValue(mixed $value): static
    {
        if (is_iterable($value)) {
            /** @phpstan-var iterable<int|string, TValue|iterable<mixed>> $value */
            return $this->merge($value);
        }

        $this->value = $value;
        return $this;
    }

    /**
     * Get container value
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
     */
    public function countValues(): array
    {
        return array_count_values(
            array_map(function ($value) {
                $value = $value->getValue();

                if (is_string($value) || is_int($value)) {
                    return $value;
                } else {
                    return null;
                }
            }, $this->items)
        );
    }



    /**
     * Convert to string
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }


    /**
     * From query string
     *
     * @return Tree<string>
     */
    public static function fromDelimitedString(
        string $string,
        string $setDelimiter = '&',
        string $valueDelimiter = '='
    ): Tree {
        if (
            $setDelimiter === '' ||
            $valueDelimiter === ''
        ) {
            throw Exceptional::UnexpectedValue('Cannot parse delimited string with empty delimiter');
        }

        /** @var static<string> */
        $output = new static();
        $parts = (array)explode($setDelimiter, $string);

        foreach ($parts as $part) {
            $valueParts = (array)explode($valueDelimiter, trim((string)$part), 2);
            $key = str_replace(['[', ']'], ['.', ''], urldecode((string)array_shift($valueParts)));
            $value = array_shift($valueParts);

            if (empty($value)) {
                $value = null;
            }

            if ($value !== null) {
                $value = urldecode($value);
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
            $key = rawurlencode($key);

            if (!empty($value) || $value === '0' || $value === 0) {
                $output[] = $key . $valueDelimiter . rawurlencode((string)$value);
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
        string $prefix = null
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

            $output = array_merge($output, $child->toDelimitedSet($urlEncode, (string)$key));
        }

        return $output;
    }


    /**
     * Map $values to values of collection as keys
     */
    public function combineWithValues(iterable $values): static
    {
        $items = array_filter(
            array_map(function ($node) {
                return $node->getValue();
            }, $this->items),
            function ($value) {
                return $value !== null;
            }
        );

        /* @phpstan-ignore-next-line */
        if (false !== ($result = array_combine($items, ArrayUtils::iterableToArray($values)))) {
            $this->clear()->merge($result);
        }

        return $this;
    }



    /**
     * Replace all values with $value
     */
    public function fill(mixed $value): static
    {
        $result = array_fill_keys(array_keys($this->items), $value);
        return $this->clear()->merge($result);
    }


    /**
     * Flip keys and values
     *
     * @return Tree<int|string>
     */
    public function flip(): Tree
    {
        $items = array_map(function ($node) {
            return (string)$node->getValue();
        }, $this->items);

        /** @var Tree<int|string> */
        $node = $this->clear();

        return $node->merge(array_flip($items));
    }



    /**
     * Merge all passed collections into one
     *
     * @phpstan-param iterable<int|string, TValue|iterable<mixed>> ...$arrays
     */
    public function merge(iterable ...$arrays): static
    {
        foreach ($arrays as $array) {
            if ($array instanceof Tree) {
                /** @phpstan-var TValue|null $value */
                $value = $array->getValue();
                $this->value = $value;

                foreach ($array->getChildren() as $key => $node) {
                    if (isset($this->items[$key])) {
                        /** @phpstan-var iterable<int|string, TValue|iterable<mixed>> $node */
                        $this->items[$key]->merge($node);
                    } else {
                        /** @phpstan-var static $newNode */
                        $newNode = clone $node;
                        $this->items[$key] = $newNode;
                    }
                }
            } else {
                foreach ($array as $key => $value) {
                    if (isset($this->items[$key])) {
                        if (is_iterable($value)) {
                            /** @phpstan-var iterable<int|string, TValue|iterable<mixed>> $value */
                            $this->items[$key]->merge($value);
                        } else {
                            $this->items[$key]->setValue($value);
                        }
                    } else {
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
    public function mergeRecursive(iterable ...$arrays): static
    {
        return $this->merge(...$arrays);
    }


    /**
     * Like merge, but replaces.. obvs
     */
    public function replace(iterable ...$arrays): static
    {
        foreach ($arrays as $array) {
            if ($array instanceof Tree) {
                /** @phpstan-var TValue|null $value */
                $value = $array->getValue();
                $this->value = $value;

                foreach ($array->getChildren() as $key => $node) {
                    /** @phpstan-var static $newNode */
                    $newNode = clone $node;
                    $this->items[$key] = $newNode;
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
    public function replaceRecursive(iterable ...$arrays): static
    {
        return $this->replace(...$arrays);
    }


    /**
     * Remove duplicates from collection
     */
    public function unique(int $flags = SORT_STRING): static
    {
        $items = array_map(function ($node) {
            return (string)$node->getValue();
        }, $this->items);

        $items = array_unique($items, $flags);
        return $this->keep(...array_map('strval', array_keys($items)));
    }


    /**
     * Recursive array conversion
     *
     * @phpstan-return array<int|string, TValue|array<mixed>|null>
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
     * Iterator interface
     *
     * @phpstan-return Iterator<int|string, static<TValue>>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->items);
    }



    /**
     * Get dump info
     *
     * @return array<int|string, mixed>
     */
    public function __debugInfo(): array
    {
        $output = [];

        foreach ($this->items as $key => $child) {
            if ($child instanceof self && empty($child->items)) {
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
     * @phpstan-param iterable<int|string, TValue|iterable<mixed>> $newItems
     * @phpstan-param TValue|null $value
     */
    protected static function propagate(
        ?iterable $newItems = [],
        mixed $value = null
    ): static {
        return new static($newItems, $value);
    }
}
