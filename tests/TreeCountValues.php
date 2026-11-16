<?php

/**
 * Collections
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Tests;

use DecodeLabs\Collections\Tree;
use ErrorException;
use RuntimeException;
use stdClass;

require dirname(__DIR__) . '/vendor/autoload.php';

set_error_handler(static function (
    int $severity,
    string $message,
    string $file,
    int $line
): never {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {
    /** @var Tree<string|int|float|bool|object> $tree */
    $tree = new Tree();

    foreach (['repeat', 'repeat', 0, '0', '', null, false, 1.5, new stdClass()] as $key => $value) {
        $tree->setNode($key, $value);
    }

    if ($tree->countValues()->toArray() !== ['repeat' => 2, 0 => 2, '' => 1]) {
        throw new RuntimeException('Tree value counts must retain zero and empty strings and skip unsupported values');
    }

    if ((new Tree())->countValues()->toArray() !== []) {
        throw new RuntimeException('An empty tree must have no value counts');
    }
} finally {
    restore_error_handler();
}

echo "Tree countValues regression checks passed.\n";
