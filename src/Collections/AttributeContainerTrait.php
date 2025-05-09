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
     * @param iterable<string,TInput> $attributes
     * @param TInput ...$attributeList
     * @return $this
     */
    public function setAttributes(
        iterable $attributes = [],
        mixed ...$attributeList
    ): static {
        foreach ($attributes as $key => $value) {
            $this->setAttribute((string)$key, $value);
        }

        foreach ($attributeList as $key => $value) {
            $this->setAttribute((string)$key, $value);
        }

        return $this;
    }

    /**
     * Replace all attributes with new map
     *
     * @param iterable<string,TInput> $attributes
     * @param TInput ...$attributeList
     */
    public function replaceAttributes(
        iterable $attributes = [],
        mixed ...$attributeList
    ): static {
        $this->clearAttributes();
        $this->setAttributes($attributes, ...$attributeList);
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
        $key = $this->normalizeAttributeKey($key);
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
        $key = $this->normalizeAttributeKey($key);
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
            $key = $this->normalizeAttributeKey($key);
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
            $key = $this->normalizeAttributeKey($key);

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
            $key = $this->normalizeAttributeKey($key);

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

    /**
     * Normalize attribute name
     */
    protected function normalizeAttributeKey(
        string $key
    ): string {
        return $key;
    }
}
