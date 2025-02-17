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
 * @implements SequenceInterface<TValue>
 * @implements ArrayAccess<int,TValue>
 * @implements IteratorAggregate<int,TValue>
 */
class ImmutableSequence implements
    ArrayAccess,
    IteratorAggregate,
    SequenceInterface
{
    /**
     * @use SequenceTrait<TValue>
     */
    use SequenceTrait;

    protected const bool Mutable = false;
}
