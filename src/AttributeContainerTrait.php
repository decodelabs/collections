<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

/**
 * @phpstan-require-implements AttributeContainer
 */
trait AttributeContainerTrait
{
    /**
     * @var array<string, mixed>
     */
    protected array $attributes = [];

    /**
     * Add attributes with map
     */
    public function setAttributes(
        array $attributes
    ): static {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Replace all attributes with new map
     */
    public function replaceAttributes(
        array $attributes
    ): static {
        return $this->clearAttributes()->setAttributes($attributes);
    }

    /**
     * Get map of current attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Replace single value
     */
    public function setAttribute(
        string $key,
        mixed $value
    ): static {
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Retrieve attribute value if set
     */
    public function getAttribute(
        string $key
    ): mixed {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Remove single attribute
     */
    public function removeAttribute(
        string ...$keys
    ): static {
        foreach ($keys as $key) {
            unset($this->attributes[$key]);
        }

        return $this;
    }

    /**
     *  Have any of these attributes been set?
     */
    public function hasAttribute(
        string ...$keys
    ): bool {
        foreach ($keys as $key) {
            if (isset($this->attributes[$key])) {
                return true;
            }
        }

        return false;
    }

    /**
     *  Have all of these attributes been set?
     */
    public function hasAttributes(
        string ...$keys
    ): bool {
        foreach ($keys as $key) {
            if (!isset($this->attributes[$key])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove all attributes
     */
    public function clearAttributes(): static
    {
        $this->attributes = [];
        return $this;
    }

    /**
     * How many attributes have been set?
     */
    public function countAttributes(): int
    {
        return count($this->attributes);
    }
}
