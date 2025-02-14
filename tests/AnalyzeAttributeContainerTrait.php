<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Tests;

use DecodeLabs\Collections\AttributeContainer;
use DecodeLabs\Collections\AttributeContainerTrait;

/**
 * @implements AttributeContainer<string|int|null>
 */
class AnalyzeAttributeContainerTrait implements AttributeContainer
{
    /**
     * @use AttributeContainerTrait<string|int|null>
     */
    use AttributeContainerTrait;
}
