<?php
/**
 * This file is part of the Collections package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Collections;

use DecodeLabs\Gadgets\Then;

interface Collection extends \Traversable, ArrayProvider, \JsonSerializable, Then
{
    public function isEmpty(): bool;
    public function isMutable(): bool;
    public function copy(): Collection;
}
