<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Tests;

use DecodeLabs\Collections\Tree;

class AnalyzeTree
{
    public function test(): void
    {
        /**
         * @var Tree<string> $tree
         */
        $tree = new Tree();

        $tree->test = 'hello';
        $tree->set('test2', 'world');
    }
}
