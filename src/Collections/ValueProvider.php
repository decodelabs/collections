<?php

/**
 * Collections
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

/**
 * @template TValue
 */
interface ValueProvider
{
    /**
     * @return TValue|null
     */
    public function getValue(): mixed;
}
