<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use DecodeLabs\Gadgets\Sanitizer;

/**
 * @template TValue
 * @extends HashMap<TValue>
 * @extends ValueProvider<TValue>
 */
interface Tree extends HashMap, ValueProvider
{
    /**
     * @param iterable<int|string, TValue|iterable>|null $items
     * @param TValue|iterable<int|string, TValue|iterable>|null $value
     */
    public function __construct(iterable $items = null, $value = null);

    /**
     * @param int|string $key
     * @param TValue|iterable<int|string, TValue|iterable>|null $value
     */
    public function __set($key, $value): void;

    /**
     * @param int|string $key
     * @return static<TValue>
     */
    public function __get($key): Tree;

    /**
     * @param int|string $key
     */
    public function __isset($key): bool;

    /**
     * @param int|string $key
     */
    public function __unset($key): void;

    /**
     * @param int|string $key
     * @param TValue|iterable<TValue>|null $value
     * @return static<TValue>
     */
    public function setNode($key, $value): Tree;

    /**
     * @param int|string $key
     * @return static<TValue>
     */
    public function getNode($key): Tree;

    /**
     * @param int|string ...$keys
     */
    public function hasNode(...$keys): bool;

    /**
     * @param int|string ...$keys
     */
    public function hasAllNodes(...$keys): bool;

    /**
     * @param int|string $key
     */
    public function sanitize($key, bool $required = true): Sanitizer;

    /**
     * @param int|string $key
     * @return mixed
     */
    public function sanitizeWith($key, callable $sanitizer, bool $required = true);

    public function sanitizeValue(bool $required = true): Sanitizer;

    /**
     * @return mixed
     */
    public function sanitizeValueWith(callable $sanitizer, bool $required = true);


    /**
     * @return TValue|null
     */
    public function pullValue();

    /**
     * @param TValue|null $value
     * @return static<TValue>
     */
    public function setValue($value): Tree;

    public function hasValue(): bool;
    public function hasAnyValue(): bool;

    /**
     * @param mixed $value
     */
    public function isValue($value, bool $strict): bool;

    /**
     * @return static<TValue>
     */
    public function removeEmpty(): Tree;


    /**
     * @return static<string>
     */
    public static function fromDelimitedString(string $string, string $setDelimiter = '&', string $valueDelimiter = '='): Tree;

    public function toDelimitedString(string $setDelimiter = '&', string $valueDelimiter = '='): string;

    /**
     * @return array<string, TValue|null>
     */
    public function toDelimitedSet(bool $urlEncode = false, string $prefix = null): array;

    /**
     * @return array<static<TValue>>
     */
    public function getChildren(): array;
}
