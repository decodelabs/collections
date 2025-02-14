<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use IteratorAggregate;

/**
 * @template TValue
 * @template TKey of int|string = int|string
 * @implements DictionaryInterface<TValue,TKey>
 * @implements IteratorAggregate<TKey,TValue>
 */
class ImmutableDictionary implements
    IteratorAggregate,
    DictionaryInterface
{
    /**
     * @use DictionaryTrait<TValue>
     */
    use DictionaryTrait;

    protected const bool Mutable = false;
}
