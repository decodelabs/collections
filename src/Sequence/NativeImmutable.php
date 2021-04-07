<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Sequence;

use DecodeLabs\Collections\Native\SequenceTrait;
use DecodeLabs\Collections\Sequence;

use IteratorAggregate;

/**
 * @template TValue
 * @implements Sequence<TValue>
 * @implements IteratorAggregate<int, TValue>
 */
class NativeImmutable implements IteratorAggregate, Sequence
{
    /**
     * @use SequenceTrait<TValue>
     */
    use SequenceTrait;

    public const MUTABLE = false;
}
