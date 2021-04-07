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

/**
 * @template TValue
 * @implements HashMap<TValue>
 * @implements IteratorAggregate<string, TValue>
 */
class NativeMutable implements IteratorAggregate, HashMap
{
    /**
     * @use HashMapTrait<TValue>
     */
    use HashMapTrait;

    public const MUTABLE = true;
}
