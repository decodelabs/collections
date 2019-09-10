<?php
/**
 * This file is part of the Collections package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Collections;

interface AttributeContainer
{
    public function setAttributes(array $attributes): AttributeContainer;
    public function replaceAttributes(array $attributes): AttributeContainer;
    public function getAttributes(): array;
    public function setAttribute(string $key, $value): AttributeContainer;
    public function getAttribute(string $key);
    public function removeAttribute(string ...$keys): AttributeContainer;
    public function hasAttribute(string ...$keys): bool;
    public function hasAttributes(string ...$keys): bool;
    public function clearAttributes(): AttributeContainer;
    public function countAttributes(): int;
}
