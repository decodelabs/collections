<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

/**
 * @template TKey
 * @template TValue
 */
interface ArrayProvider
{
    /**
     * @phpstan-return array<TKey, TValue>
     */
    public function toArray(): array;
}
