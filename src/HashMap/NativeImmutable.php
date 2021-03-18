<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\HashMap;

use DecodeLabs\Collections\HashMap;
use DecodeLabs\Collections\Native\HashMapTrait;

use IteratorAggregate;

class NativeImmutable implements IteratorAggregate, HashMap
{
    use HashMapTrait;

    public const MUTABLE = false;
}
