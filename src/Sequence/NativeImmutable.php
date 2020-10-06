<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Sequence;

use DecodeLabs\Collections\Native\SequenceTrait;
use DecodeLabs\Collections\Sequence;

class NativeImmutable implements \IteratorAggregate, Sequence
{
    use SequenceTrait;

    public const MUTABLE = false;
}
