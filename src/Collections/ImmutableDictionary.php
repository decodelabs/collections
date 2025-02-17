<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use ArrayAccess;
use IteratorAggregate;

/**
 * @template TValue
 * @implements DictionaryInterface<TValue>
 * @implements ArrayAccess<int|string,TValue>
 * @implements IteratorAggregate<int|string,TValue>
 */
class ImmutableDictionary implements
    ArrayAccess,
    IteratorAggregate,
    DictionaryInterface
{
    /**
     * @use DictionaryTrait<TValue>
     */
    use DictionaryTrait;

    protected const bool Mutable = false;
}
