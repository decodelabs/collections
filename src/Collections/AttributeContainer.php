<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

/**
 * @template TValue
 * @template TInput = TValue
 */
interface AttributeContainer
{
    /**
     * @param iterable<string,TInput> $attributes
     * @param TInput ...$attributeList
     * @return $this
     */
    public function setAttributes(
        iterable $attributes = [],
        mixed ...$attributeList
    ): static;

    /**
     * @param iterable<string,TInput> $attributes
     * @param TInput ...$attributeList
     * @return $this
     */
    public function replaceAttributes(
        iterable $attributes = [],
        mixed ...$attributeList
    ): static;

    /**
     * @return array<string,TValue>
     */
    public function getAttributes(): array;

    /**
     * @param TInput $value
     * @return $this
     */
    public function setAttribute(
        string $key,
        mixed $value
    ): static;

    /**
     * @return ?TValue
     */
    public function getAttribute(
        string $key
    ): mixed;

    /**
     * @return $this
     */
    public function removeAttribute(
        string ...$keys
    ): static;

    public function hasAttribute(
        string ...$keys
    ): bool;

    public function hasAttributes(
        string ...$keys
    ): bool;


    /**
     * @return $this
     */
    public function clearAttributes(): static;

    public function countAttributes(): int;
}
