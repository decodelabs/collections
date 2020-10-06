<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use DecodeLabs\Gadgets\Sanitizer;

interface Tree extends HashMap, ValueProvider
{
    public function __set(string $key, $value): Tree;
    public function __get(string $key): Tree;
    public function __isset(string $key): bool;
    public function __unset(string $key): void;

    public function setNode(string $key, $value): Tree;
    public function getNode(string $key): Tree;
    public function hasNode(string ...$keys): bool;
    public function hasAllNodes(string ...$keys): bool;

    public function sanitize(string $key, bool $required = true): Sanitizer;
    public function sanitizeWith(string $key, callable $sanitizer, bool $required = true);
    public function sanitizeValue(bool $required = true): Sanitizer;
    public function sanitizeValueWith(callable $sanitizer, bool $required = true);

    public function setValue($value): HashMap;
    public function hasValue(): bool;
    public function hasAnyValue(): bool;
    public function isValue($value, bool $strict): bool;

    public static function fromDelimitedString(string $string, string $setDelimiter = '&', string $valueDelimiter = '='): Tree;
    public function toDelimitedString(string $setDelimiter = '&', string $valueDelimiter = '='): string;
    public function toDelimitedSet(bool $urlEncode = false, string $prefix = null): array;

    public function getChildren(): array;
}
