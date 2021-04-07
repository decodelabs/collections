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
    public function setAttributes(array $attributes): AttributeContainer;

    /**
     * @param array<string, mixed> $attributes
     * @return $this
     */
    public function replaceAttributes(array $attributes): AttributeContainer;

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array;

    /**
     * @param mixed $value
     * @return $this
     */
    public function setAttribute(string $key, $value): AttributeContainer;

    /**
     * @return mixed
     */
    public function getAttribute(string $key);

    /**
     * @return $this
     */
    public function removeAttribute(string ...$keys): AttributeContainer;
    public function hasAttribute(string ...$keys): bool;
    public function hasAttributes(string ...$keys): bool;

    /**
     * @return $this
     */
    public function clearAttributes(): AttributeContainer;

    public function countAttributes(): int;
}
