<?php
/**
 * This file is part of the Collections package
 * @license http://opensource.org/licenses/MIT
 */
declare(strict_types=1);
namespace DecodeLabs\Collections;

interface ArrayProvider
{
    public function toArray(): array;
}
