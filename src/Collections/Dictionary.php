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
 * @template TKey of int|string = int|string
 * @implements DictionaryInterface<TValue>
 * @implements ArrayAccess<TKey,TValue>
 * @implements IteratorAggregate<TKey,TValue>
 */
class Dictionary implements
    ArrayAccess,
    IteratorAggregate,
    DictionaryInterface
{
    /**
     * @use DictionaryTrait<TValue>
     */
    use DictionaryTrait;

    protected const bool Mutable = true;
}
