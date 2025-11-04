<?php

/**
 * Collections
 * @license https://opensource.org/licenses/MIT
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
     * @return array<string,TValue>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
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
     * @return ?TValue
     */
    public function getAttribute(
        string $key
    ): mixed {
        $key = $this->normalizeAttributeKey($key);
        return $this->attributes[$key] ?? null;
    }

    /**
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
     * @return $this
     */
    public function clearAttributes(): static
    {
        $this->attributes = [];
        return $this;
    }

    public function countAttributes(): int
    {
        return count($this->attributes);
    }

    protected function normalizeAttributeKey(
        string $key
    ): string {
        return $key;
    }
}
