<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Tree;

use DecodeLabs\Collections\ArrayUtils;
use DecodeLabs\Collections\HashMap;
use DecodeLabs\Collections\Native\HashMapTrait;
use DecodeLabs\Collections\Tree;

use DecodeLabs\Gadgets\Sanitizer;
use DecodeLabs\Exceptional;

class NativeMutable implements \IteratorAggregate, Tree
{
    use HashMapTrait;

    public const MUTABLE = true;
    public const KEY_SEPARATOR = '.';

    protected $value;

    /**
     * Value based construct
     */
    public function __construct(iterable $items = null, $value = null)
    {
        $this->value = $value;

        if ($items !== null) {
            $this->merge(ArrayUtils::iterableToArray($items));
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
    public function __set(string $key, $value): void
    {
        if (is_iterable($value)) {
            $items = $value;
            $value = null;
        } else {
            $items = [];
        }

        $this->items[$key] = $this->propagate($items, $value);
    }

    /**
     * Get node
     */
    public function __get(string $key): Tree
    {
        if (!array_key_exists($key, $this->items)) {
            $this->items[$key] = $this->propagate();
        }

        return $this->items[$key];
    }

    /**
     * Check for node
     */
    public function __isset(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    /**
     * Remove node
     */
    public function __unset(string $key): void
    {
        unset($this->items[$key]);
    }



    /**
     * Set value by dot access
     */
    public function setNode(string $key, $value): Tree
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
    public function getNode(string $key): Tree
    {
        if (empty(static::KEY_SEPARATOR)) {
            return $this->__get($key);
        }

        $node = $this;
        $parts = explode(static::KEY_SEPARATOR, $key);

        foreach ($parts as $part) {
            $node = $node->__get($part);
        }

        return $node;
    }

    /**
     * True if any provided keys exist as a node
     */
    public function hasNode(string ...$keys): bool
    {
        if (empty(static::KEY_SEPARATOR)) {
            foreach ($keys as $key) {
                if (isset($this->items[$key])) {
                    return true;
                }
            }
        } else {
            foreach ($keys as $key) {
                $parts = explode(static::KEY_SEPARATOR, $key);
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
    public function hasAllNodes(string ...$keys): bool
    {
        if (empty(static::KEY_SEPARATOR)) {
            foreach ($keys as $key) {
                if (!isset($this->items[$key])) {
                    return false;
                }
            }
        } else {
            foreach ($keys as $key) {
                $parts = explode(static::KEY_SEPARATOR, $key);
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
     * Get value
     */
    public function get(string $key)
    {
        return $this->getNode($key)->getValue();
    }

    /**
     * Set value on node
     */
    public function set(string $key, $value): HashMap
    {
        $this->getNode($key)->setValue($value);
        return $this;
    }

    /**
     * True if any provided keys have a set value (not null)
     */
    public function has(string ...$keys): bool
    {
        if (empty(static::KEY_SEPARATOR)) {
            foreach ($keys as $key) {
                if (isset($this->items[$key]) && $this->items[$key]->hasValue()) {
                    return true;
                }
            }
        } else {
            foreach ($keys as $key) {
                $parts = explode(static::KEY_SEPARATOR, $key);
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
    public function hasAll(string ...$keys): bool
    {
        if (empty(static::KEY_SEPARATOR)) {
            foreach ($keys as $key) {
                if (!(isset($this->items[$key]) && $this->items[$key]->hasValue())) {
                    return false;
                }
            }
        } else {
            foreach ($keys as $key) {
                $parts = explode(static::KEY_SEPARATOR, $key);
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
     * Remove empty nodes
     */
    public function removeEmpty(): HashMap
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
     */
    public function offsetSet($key, $value): void
    {
        if ($key === null) {
            if (is_iterable($value)) {
                $this->items[] = $this->propagate($value);
            } else {
                $this->items[] = $this->propagate(null, $value);
            }
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
    public function sanitize(string $key, bool $required = true): Sanitizer
    {
        return $this->getNode($key)->sanitizeValue($required);
    }

    /**
     * Get node and sanitize with custom sanitizer
     */
    public function sanitizeWith(string $key, callable $sanitizer, bool $required = true)
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
    public function setValue($value): HashMap
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
     * Convert to string
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }


    /**
     * From query string
     */
    public static function fromDelimitedString(string $string, string $setDelimiter = '&', string $valueDelimiter = '='): Tree
    {
        if (
            empty($setDelimiter) ||
            empty($valueDelimiter)
        ) {
            throw Exceptional::UnexpectedValue('Cannot parse delimited string with empty delimiter');
        }

        $output = static::propagate();
        $parts = explode($setDelimiter, $string);

        foreach ($parts as $part) {
            $valueParts = explode($valueDelimiter, trim($part), 2);
            $key = str_replace(['[', ']'], ['.', ''], urldecode(array_shift($valueParts)));
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

        if ($prefix !== null &&
            ($this->value !== null || empty($this->items))) {
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
        $items = array_map(function ($node) {
            return $node->getValue();
        }, $this->items);

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
     */
    public function flip(): HashMap
    {
        $items = array_map(function ($node) {
            return (string)$node->getValue();
        }, $this->items);

        return $this->clear()->merge(array_flip($items));
    }



    /**
     * Merge all passed collections into one
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
                        $this->items[$key] = clone $node;
                    }
                }
            } else {
                foreach ($array as $key => $value) {
                    if (is_iterable($value)) {
                        if (isset($this->items[$key])) {
                            $this->items[$key]->merge($value);
                        } else {
                            $this->items[$key] = $this->propagate($value);
                        }
                    } else {
                        if (isset($this->items[$key])) {
                            $this->items[$key]->setValue($value);
                        } else {
                            $this->items[$key] = $this->propagate(null, $value);
                        }
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
                    $this->items[$key] = clone $node;
                }
            } else {
                foreach ($array as $key => $value) {
                    if (is_iterable($value)) {
                        $this->items[$key] = $this->propagate($value);
                    } else {
                        $this->items[$key] = $this->propagate(null, $value);
                    }
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
     * Get dump info
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
     */
    protected static function propagate(?iterable $newItems = [], $value = null): Tree
    {
        return new self($newItems, $value);
    }
}
