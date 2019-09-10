<?php
/**
 * This file is part of the Collections package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Collections\Sequence;

use DecodeLabs\Collections\Sequence;
use DecodeLabs\Collections\Native\SequenceTrait;

class NativeImmutable implements \IteratorAggregate, Sequence
{
    use SequenceTrait;

    const MUTABLE = false;
}
