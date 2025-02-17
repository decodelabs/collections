<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

/**
 * @phpstan-require-implements AttributeContainer
 * @template TValue
 * @template TInput = TValue
 */
trait AttributeContainerTrait
{
    /**
     * @var array<string,TValue>
     */
    protected array $attributes = [];

    /**
     * Add attributes with map
     *
     * @param array<string,TInput> $attributes
     * @return $this
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
     *
     * @param array<string,TInput> $attributes
     */
    public function replaceAttributes(
        array $attributes
    ): static {
        $this->clearAttributes();
        $this->setAttributes($attributes);
        return $this;
    }

    /**
     * Get map of current attributes
     *
     * @return array<string,TValue>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Replace single value
     *
     * @param TInput $value
     */
    public function setAttribute(
        string $key,
        mixed $value
    ): static {
        // @phpstan-ignore-next-line PHPStan bug
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Retrieve attribute value if set
     *
     * @return ?TValue
     */
    public function getAttribute(
        string $key
    ): mixed {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Remove single attribute
     *
     * @return $this
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
     *
     * @return $this
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
