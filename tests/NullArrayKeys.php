<?php

/**
 * Collections
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections\Tests\NullArrayKeys;

require dirname(__DIR__) . '/vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

function expectSame(mixed $expected, mixed $actual): void
{
    if ($expected !== $actual) {
        throw new \RuntimeException('Unexpected null-key regression result: ' . var_export($actual, true));
    }
}

try {
    /** @return \Generator<int|string|null,string|null> */
    function keyedValues(): \Generator
    {
        yield null => 'first';
        yield '' => 'empty';
        yield '0' => 'zero';
        yield 'named' => null;
    }

    $expected = ['' => 'empty', 0 => 'zero', 'named' => null];
    expectSame($expected, \DecodeLabs\Collections\ArrayUtils::iterableToArray(keyedValues()));
    foreach (['merge', 'replace'] as $method) {
        $tree = new \DecodeLabs\Collections\Tree();
        (new \ReflectionMethod($tree, $method))->invoke($tree, keyedValues());
        expectSame($expected, $tree->toArray());
    }

    $nested = new \DecodeLabs\Collections\Tree();
    $nested->merge((static function (): \Generator {
        yield null => ['first' => 1];
        yield '' => ['second' => 2];
    })());
    expectSame(['' => ['first' => 1, 'second' => 2]], $nested->toArray());

    foreach ([\DecodeLabs\Collections\Dictionary::class, \DecodeLabs\Collections\ImmutableDictionary::class] as $class) {
        $dictionary = new $class(['' => 'empty', 'nil' => null, 0 => 'zero']);
        foreach (['get', 'pull'] as $method) {
            $copy = clone $dictionary;
            expectSame('empty', (new \ReflectionMethod($copy, $method))->invoke($copy, null));
            expectSame($class === \DecodeLabs\Collections\ImmutableDictionary::class || $method !== 'pull', $copy->hasKey(''));
        }
        /** @var \DecodeLabs\Collections\DictionaryInterface<string|null> $changed */
        $changed = (new \ReflectionMethod($dictionary, 'set'))->invoke($dictionary, null, 'updated');
        expectSame('updated', $changed->get(''));
        foreach (['has', 'hasAll', 'hasKey', 'hasKeys'] as $method) {
            expectSame(true, (new \ReflectionMethod($dictionary, $method))->invoke($dictionary, null));
        }
        expectSame(false, $dictionary->has('nil'));
        expectSame(true, $dictionary->hasKey('nil'));
        expectSame('zero', $dictionary->get('0'));
    }

    $mapped = (new \DecodeLabs\Collections\Dictionary(['source']))->mapSelf(static function (): \Generator {
        yield null => 'first';
        yield '' => 'last';
        yield 0 => 'zero';
    });
    expectSame(['' => 'last', 0 => 'zero'], $mapped->toArray());

    $rows = [
        ['id' => null, 'name' => 'null'],
        ['id' => '', 'name' => 'empty'],
        ['id' => '0', 'name' => 'string zero'],
        ['id' => 0, 'name' => 'zero'],
        ['name' => 'missing id'],
        ['id' => 'skip'],
        (object)['id' => 'object', 'name' => null],
    ];
    $collection = new \DecodeLabs\Collections\Dictionary($rows);
    expectSame(['empty', 'zero', 'missing id', null], $collection->pluck('name', 'id'));
    expectSame(['null', 'empty', 'string zero', 'zero', 'missing id', null], $collection->pluck('name'));
    expectSame([], (new \DecodeLabs\Collections\Dictionary([]))->pluck('name', 'id'));

    /** @var \DecodeLabs\Collections\Sequence<string> $sequence */
    $sequence = new \DecodeLabs\Collections\Sequence([]);
    $sequence[] = 'appended';
    expectSame(['appended'], $sequence->toArray());

    /** @var \DecodeLabs\Collections\Dictionary<string> $map */
    $map = new \DecodeLabs\Collections\Dictionary([]);
    try {
        $map[] = 'invalid append';
        throw new \RuntimeException('Map append must still be rejected');
    } catch (\Throwable $error) {
        if ($error->getMessage() !== 'Cannot push to Maps') {
            throw $error;
        }
    }
} finally {
    restore_error_handler();
}

echo "Null array-key regression checks passed.\n";
