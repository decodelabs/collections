<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

/**
 * @template TValue
 * @template TKey of int|string = int|string
 * @extends MapInterface<TKey,TValue,TValue>
 */
interface DictionaryInterface extends MapInterface
{
}
