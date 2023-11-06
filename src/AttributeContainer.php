<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

interface AttributeContainer
{
    /**
     * @param array<string, mixed> $attributes
     * @return $this
     */
    public function setAttributes(
        array $attributes
    ): static;

    /**
     * @param array<string, mixed> $attributes
     * @return $this
     */
    public function replaceAttributes(
        array $attributes
    ): static;

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array;

    /**
     * @return $this
     */
    public function setAttribute(
        string $key,
        mixed $value
    ): static;

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
