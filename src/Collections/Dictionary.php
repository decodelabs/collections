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
 * @implements DictionaryInterface<TValue>
 * @implements IteratorAggregate<int|string,TValue>
 */
class Dictionary implements
    IteratorAggregate,
    DictionaryInterface
{
    /**
     * @use DictionaryTrait<TValue>
     */
    use DictionaryTrait;

    protected const bool Mutable = true;
}
