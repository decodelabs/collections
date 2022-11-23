<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use DecodeLabs\Lucid\Provider\MixedContext as SanitizerProvider;

/**
 * @template TValue
 * @extends MixedMap<TValue, static>
 * @extends ValueProvider<TValue>
 * @extends SanitizerProvider<TValue>
 */
interface Tree extends
    MixedMap,
    ValueProvider,
    SanitizerProvider
{
    /**
     * @param iterable<int|string, TValue|iterable<mixed>>|null $items
     * @phpstan-param TValue|iterable<int|string, TValue|iterable<mixed>>|null $value
     */
    public function __construct(
        iterable $items = null,
        mixed $value = null
    );

    /**
     * @phpstan-param TValue|iterable<int|string, TValue|iterable<mixed>>|null $value
     */
    public function __set(
        int|string $key,
        mixed $value
    ): void;

    public function __get(int|string $key): static;
    public function __isset(int|string $key): bool;
    public function __unset(int|string $key): void;

    /**
     * @phpstan-param TValue|iterable<TValue>|null $value
     */
    public function setNode(
        int|string $key,
        mixed $value
    ): static;

    public function getNode(int|string $key): static;
    public function hasNode(int|string ...$keys): bool;
    public function hasAllNodes(int|string ...$keys): bool;

    /**
     * @phpstan-return TValue|null
     */
    public function pullValue(): mixed;

    /**
     * @phpstan-param TValue|null $value
     */
    public function setValue(mixed $value): static;

    public function hasValue(): bool;
    public function hasAnyValue(): bool;

    public function isValue(
        mixed $value,
        bool $strict
    ): bool;

    public function removeEmpty(): static;


    /**
     * @return Tree<string>
     */
    public static function fromDelimitedString(
        string $string,
        string $setDelimiter = '&',
        string $valueDelimiter = '='
    ): Tree;

    public function toDelimitedString(
        string $setDelimiter = '&',
        string $valueDelimiter = '='
    ): string;

    /**
     * @phpstan-return array<string, TValue|null>
     */
    public function toDelimitedSet(
        bool $urlEncode = false,
        string $prefix = null
    ): array;

    /**
     * @phpstan-return array<static<TValue>>
     */
    public function getChildren(): array;
}
