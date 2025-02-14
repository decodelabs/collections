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
 * @implements SequenceInterface<TValue>
 * @implements IteratorAggregate<int,TValue>
 */
class Sequence implements
    IteratorAggregate,
    SequenceInterface
{
    /**
     * @use SequenceTrait<TValue>
     */
    use SequenceTrait;

    protected const bool Mutable = true;
}
