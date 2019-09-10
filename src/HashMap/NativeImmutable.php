<?php
/**
 * This file is part of the Collections package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Collections\HashMap;

use DecodeLabs\Collections\HashMap;
use DecodeLabs\Collections\Native\HashMapTrait;

class NativeImmutable implements \IteratorAggregate, HashMap
{
    use HashMapTrait;

    const MUTABLE = false;
}
