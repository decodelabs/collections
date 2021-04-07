<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Tree;

use ArrayIterator;

use DecodeLabs\Collections\ArrayUtils;
use DecodeLabs\Collections\HashMap;
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
class NativeMutable implements IteratorAggregate, Tree
{
    /**
     * @use HashMapTrait<TValue>
     */
    use HashMapTrait;

    public const MUTABLE = true;
    public const KEY_SEPARATOR = '.';

    /**
     * @var TValue|null
     */
    protected $value;

    /**
     * @var array<int|string, static<TValue>>
     */
    protected $items = [];

    /**
     * Value based construct
     */
    public function __construct(iterable $items = null, $value = null)
    {
        if (!is_iterable($value)) {
            $this->value = $value;
        }

        if ($items !== null) {
            $this->merge($items);
        }

        if (is_iterable($value)) {
            $this->merge($value);
        }
    }


    /**
     * Clone whole tree
     */
    public function __clone()
    {
        foreach ($this->items as $key => $child) {
            $this->items[$key] = clone $child;
        }
    }



    /**
     * Set node value
     */
    public function __set($key, $value): void
    {
        $this->items[$key] = new static(null, $value);
    }

    /**
     * Get node
     */
    public function __get($key): Tree
    {
        if (!array_key_exists($key, $this->items)) {
            $this->items[$key] = new static();
        }

        return $this->items[$key];
    }

    /**
     * Check for node
     */
    public function __isset($key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Remove node
     */
    public function __unset($key): void
    {
        unset($this->items[$key]);
    }



    /**
     * Set value by dot access
     */
    public function setNode($key, $value): Tree
    {
        $node = $this->getNode($key);

        if (is_iterable($value)) {
            $node->clear()->merge($value);
        } else {
            $node->setValue($value);
        }

        return $this;
    }

    /**
     * Get node by dot access
     */
    public function getNode($key): Tree
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
    public function hasNode(...$keys): bool
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
    public function hasAllNodes(...$keys): bool
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
     * @param int|string $key
     * @return array<int|string>
     */
    protected function splitNodeKey($key): array
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
    public function get($key)
    {
        return $this->getNode($key)->getValue();
    }

    /**
     * Retrieve entry and remove from collection
     *
     * @return TValue|null
     */
    public function pull($key)
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
    public function set($key, $value): HashMap
    {
        $this->getNode($key)->setValue($value);
        return $this;
    }

    /**
     * True if any provided keys have a set value (not null)
     */
    public function has(...$keys): bool
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
    public function hasAll(...$keys): bool
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
    public function pop()
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
    public function shift()
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
    public function removeEmpty(): Tree
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
    public function findKey($value, bool $strict = false): ?string
    {
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
    public function clear(): HashMap
    {
        $this->value = null;
        $this->items = [];
        return $this;
    }




    /**
     * Set by array access
     *
     * @param TValue|iterable<int|string, TValue|iterable>|null $value
     */
    public function offsetSet($key, $value): void
    {
        if ($key === null) {
            $this->items[] = new static(null, $value);
        } elseif (is_iterable($value)) {
            $this->getNode($key)->merge($value);
        } else {
            $this->getNode($key)->setValue($value);
        }
    }

    /**
     * Get by array access
     */
    public function offsetGet($key)
    {
        return $this->getNode($key)->getValue();
    }

    /**
     * Check by array access
     */
    public function offsetExists($key)
    {
        return $this->getNode($key)->hasValue();
    }



    /**
     * Get node and return value sanitizer
     */
    public function sanitize($key, bool $required = true): Sanitizer
    {
        return $this->getNode($key)->sanitizeValue($required);
    }

    /**
     * Get node and sanitize with custom sanitizer
     */
    public function sanitizeWith($key, callable $sanitizer, bool $required = true)
    {
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
    public function sanitizeValueWith(callable $sanitizer, bool $required = true)
    {
        return $this->sanitizeValue($required)->with($sanitizer);
    }




    /**
     * Set container value
     */
    public function setValue($value): Tree
    {
        if (is_iterable($value)) {
            return $this->merge($value);
        }

        $this->value = $value;
        return $this;
    }

    /**
     * Get container value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get container value and remove
     */
    public function pullValue()
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
    public function isValue($value, bool $strict): bool
    {
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
     * @return static<string>
     */
    public static function fromDelimitedString(string $string, string $setDelimiter = '&', string $valueDelimiter = '='): Tree
    {
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
    public function toDelimitedString(string $setDelimiter = '&', string $valueDelimiter = '='): string
    {
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
    public function toDelimitedSet(bool $urlEncode = false, string $prefix = null): array
    {
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
    public function combineWithValues(iterable $values): HashMap
    {
        $items = array_filter(
            array_map(function ($node) {
                return $node->getValue();
            }, $this->items),
            function ($value) {
                return $value !== null;
            }
        );

        if (false !== ($result = array_combine($items, ArrayUtils::iterableToArray($values)))) {
            $this->clear()->merge($result);
        }

        return $this;
    }



    /**
     * Replace all values with $value
     */
    public function fill($value): HashMap
    {
        $result = array_fill_keys(array_keys($this->items), $value);
        return $this->clear()->merge($result);
    }


    /**
     * Flip keys and values
     *
     * @return static<int|string>
     */
    public function flip(): HashMap
    {
        $items = array_map(function ($node) {
            return (string)$node->getValue();
        }, $this->items);

        /** @var static<int|string> */
        $node = $this->clear();

        return $node->merge(array_flip($items));
    }



    /**
     * Merge all passed collections into one
     *
     * @param iterable<int|string, TValue|iterable> ...$arrays
     * @return static<TValue>
     */
    public function merge(iterable ...$arrays): HashMap
    {
        foreach ($arrays as $array) {
            if ($array instanceof Tree) {
                $this->value = $array->getValue();

                foreach ($array->getChildren() as $key => $node) {
                    if (isset($this->items[$key])) {
                        $this->items[$key]->merge($node);
                    } else {
                        /** @var static<TValue> */
                        $newNode = clone $node;
                        $this->items[$key] = $newNode;
                    }
                }
            } else {
                foreach ($array as $key => $value) {
                    if (isset($this->items[$key])) {
                        if (is_iterable($value)) {
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
    public function mergeRecursive(iterable ...$arrays): HashMap
    {
        return $this->merge(...$arrays);
    }


    /**
     * Like merge, but replaces.. obvs
     */
    public function replace(iterable ...$arrays): HashMap
    {
        foreach ($arrays as $array) {
            if ($array instanceof Tree) {
                $this->value = $array->getValue();

                foreach ($array->getChildren() as $key => $node) {
                    /** @var static<TValue> */
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
    public function replaceRecursive(iterable ...$arrays): HashMap
    {
        return $this->replace(...$arrays);
    }


    /**
     * Remove duplicates from collection
     */
    public function unique(int $flags = SORT_STRING): HashMap
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
     * @return array<int|string, TValue|array|null>
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
     * @return Iterator<int|string, static<TValue>>
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
                    '⇒ value' => $this->value
                ];
            } else {
                return [];
            }
        }

        if ($this->value !== null) {
            $output = [
                '⇒ value' => $this->value,
                '⇒ children' => $output
            ];
        }

        return $output;
    }



    /**
     * Copy and reinitialise new object
     *
     * @param iterable<int|string, TValue|iterable> $newItems
     * @param TValue|null $value
     * @return static<TValue>
     */
    protected static function propagate(?iterable $newItems = [], $value = null): Tree
    {
        return new static($newItems, $value);
    }
}
