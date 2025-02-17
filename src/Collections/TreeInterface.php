<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use DecodeLabs\Lucid\Provider\MixedContext as SanitizerProvider;

/**
 * @template TValue of string|bool|int|float|resource|object
 * @phpstan-type ChildList = iterable<int|string,TValue|iterable<int|string,TValue|iterable<mixed>>>
 * @extends MapInterface<int|string,TValue,static>
 * @extends ValueProvider<TValue>
 * @extends SanitizerProvider<TValue>
 */
interface TreeInterface extends
    MapInterface,
    ValueProvider,
    SanitizerProvider
{
    /**
     * @param ChildList|null $items
     * @param TValue|ChildList|null $value
     */
    public function __construct(
        ?iterable $items = null,
        mixed $value = null
    );

    /**
     * @param int|string $key
     * @param TValue|ChildList|null $value
     */
    public function __set(
        int|string $key,
        mixed $value
    ): void;

    /**
     * @param int|string $key
     */
    public function __get(
        int|string $key
    ): static;

    /**
     * @param int|string $key
     */
    public function __isset(
        int|string $key
    ): bool;

    /**
     * @param int|string $key
     */
    public function __unset(
        int|string $key
    ): void;


    /**
     * @param TValue|iterable<TValue>|null $value
     */
    public function setNode(
        int|string $key,
        mixed $value
    ): static;

    /**
     * @param int|string $key
     */
    public function getNode(
        int|string $key
    ): static;

    /**
     * @param int|string ...$keys
     */
    public function hasNode(
        int|string ...$keys
    ): bool;

    /**
     * @param int|string ...$keys
     */
    public function hasAllNodes(
        int|string ...$keys
    ): bool;


    /**
     * @return ?TValue
     */
    public function pullValue(): mixed;

    /**
     * @param ?TValue $value
     */
    public function setValue(
        mixed $value
    ): static;

    public function hasValue(): bool;
    public function hasAnyValue(): bool;

    public function isValue(
        mixed $value,
        bool $strict
    ): bool;

    public function removeEmpty(): static;


    /**
     * @param ChildList ...$arrays
     */
    public function merge(
        iterable ...$arrays
    ): static;

    /**
     * @param ChildList ...$arrays
     */
    public function mergeRecursive(
        iterable ...$arrays
    ): static;


    /**
     * @return TreeInterface<string>
     */
    public static function fromDelimitedString(
        string $string,
        string $setDelimiter = '&',
        string $valueDelimiter = '='
    ): TreeInterface;

    public function toDelimitedString(
        string $setDelimiter = '&',
        string $valueDelimiter = '='
    ): string;

    /**
     * @return array<string,?TValue>
     */
    public function toDelimitedSet(
        bool $urlEncode = false,
        ?string $prefix = null
    ): array;

    /**
     * @return array<int|string,static>
     */
    public function getChildren(): array;
}
