<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\HashMap;

use DecodeLabs\Collections\HashMap;
use DecodeLabs\Collections\Native\HashMapTrait;

class NativeMutable implements \IteratorAggregate, HashMap
{
    use HashMapTrait;

    public const MUTABLE = true;
}
