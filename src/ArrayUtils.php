<?php

/**
 * @package Collections
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Collections;

use DecodeLabs\Exceptional;

use Generator;

class ArrayUtils
{
    /**
     * Collapse multi-dimensional collections to flat array
     *
     * @param iterable<string|int, mixed> $data
     * @return array<string|int, mixed>
     */
    public static function collapse(iterable $data, bool $keepKeys = true, bool $unique = false, bool $removeNull = false): array
    {
        $output = [];
        $sort = SORT_STRING;

        foreach ($data as $key => $value) {
            if ($value === null && $removeNull) {
                continue;
            }

            if ($isIterable = is_iterable($value)) {
                $children = $value;
            } else {
                $children = null;
            }

            if ($isContainer = $value instanceof ValueProvider) {
                $value = $value->getValue();
            }

            if (
                (!$isIterable || $isContainer) &&
                (!$removeNull || $value !== null)
            ) {
                if (is_object($value)) {
                    $sort = SORT_REGULAR;
                }

                if ($keepKeys && is_string($key)) {
                    $output[$key] = $value;
                } else {
                    $output[] = $value;
                }
            }

            if (
                $isIterable &&
                $children !== null
            ) {
                $output = array_merge($output, self::collapse(
                    $children,
                    $unique,
                    $removeNull
                ));
            }
        }

        if ($unique) {
            return array_unique($output, $sort);
        } else {
            return $output;
        }
    }


    /**
     * Generator, scanning all non-container nodes
     *
     * @param iterable<string|int, mixed> $data
     * @return Generator<string|int, mixed>
     */
    public static function scanValues(iterable $data, bool $removeNull = false): Generator
    {
        foreach ($data as $key => $value) {
            if ($isIterable = is_iterable($value)) {
                $children = $value;
            } else {
                $children = null;
            }

            if ($isContainer = $value instanceof ValueProvider) {
                $value = $value->getValue();
            }

            if (
                (!$isIterable || $isContainer) &&
                (!$removeNull || $value !== null)
            ) {
                yield $key => $value;
            }

            if (
                $isIterable &&
                $children !== null
            ) {
                yield from self::scanValues($children, $removeNull);
            }
        }
    }


    /**
     * Check array is associative
     *
     * @param array<string|int, mixed> $array
     */
    public static function isAssoc(array $array): bool
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }



    /**
     * Get first of a collection
     *
     * @template TKey
     * @template TValue
     * @param iterable<TKey, TValue> $data
     * @return TValue|null
     */
    public static function getFirst(iterable $data, callable $filter = null, object $callbackTarget = null)
    {
        foreach ($data as $key => $item) {
            if ($filter !== null && !$filter($item, $key, $callbackTarget)) {
                continue;
            }

            return $item;
        }

        return null;
    }

    /**
     * Get last item in an array
     *
     * @template TKey
     * @template TValue
     * @param iterable<TKey, TValue> $data
     * @return TValue|null
     */
    public static function getLast(iterable $data, callable $filter = null, object $callbackTarget = null)
    {
        $array = self::iterableToArray($data);

        if (!$filter) {
            if (empty($array)) {
                return null;
            }

            return $array[array_key_last($array)];
        }

        return self::getFirst(array_reverse($array, true), $filter, $callbackTarget);
    }

    /**
     * Get random item in array
     *
     * @template TKey
     * @template TValue
     * @param array<TKey, TValue> $array
     * @return TValue
     */
    public static function getRandom(array $array)
    {
        if (empty($array)) {
            throw Exceptional::Underflow(
                'Cannot pick random, array is empty'
            );
        }

        return $array[array_rand($array)];
    }

    /**
     * Get random subset from array
     *
     * @template TKey
     * @template TValue
     * @param array<TKey, TValue> $array
     * @return array<TKey, TValue>
     */
    public static function sliceRandom(array $array, int $number): array
    {
        $count = count($array);

        if ($number < 1) {
            return [];
        }

        if ($number > $count) {
            throw Exceptional::Underflow(
                'Cannot random slice ' . $number . ' items, only ' . $count . ' items in array'
            );
        }

        return array_intersect_key($array, array_flip((array)array_rand($array, $number)));
    }

    /**
     * Key base shuffling
     *
     * @template TKey
     * @template TValue
     * @param array<TKey, TValue> $array
     * @return array<TKey, TValue>
     */
    public static function kshuffle(array $array): array
    {
        uksort($array, function (): int {
            return rand() > getrandmax() / 2 ?
                1 : 0;
        });

        return $array;
    }

    /**
     * Get subset based on key match
     *
     * @template TKey
     * @template TValue
     * @param array<TKey, TValue> $array
     * @param array<TKey> $keys
     * @return array<TKey, TValue>
     */
    public static function intersectKeys(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip($keys));
    }

    /**
     * Filter an array
     *
     * @template TKey
     * @template TValue
     * @param array<TKey, TValue> $array
     * @return array<TKey, TValue>
     */
    public static function filter(array $array, callable $filter): array
    {
        return array_filter($array, $filter, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Convert iterable to array
     *
     * @template TKey
     * @template TValue
     * @param iterable<TKey, TValue> $iterable
     * @return array<TKey, TValue>
     */
    public static function iterableToArray(iterable $iterable): array
    {
        if (is_array($iterable)) {
            return $iterable;
        }

        if ($iterable instanceof \JsonSerializable) {
            return $iterable->jsonSerialize();
        }

        if (!$iterable instanceof \Traversable) {
            $iterable = function () use ($iterable) {
                yield from $iterable;
            };
        }

        return iterator_to_array($iterable);
    }

    /**
     * Convert list of iterables to arrays
     *
     * @template TKey
     * @template TValue
     * @param iterable<TKey, TValue> ...$iterables
     * @return array<array<TKey, TValue>>
     */
    public static function iterablesToArrays(iterable ...$iterables): array
    {
        $output = [];

        foreach ($iterables as $i => $iterable) {
            $output[$i] = self::iterableToArray($iterable);
        }

        return $output;
    }

    /**
     * Multi dimensional in_array
     *
     * @param mixed $value
     * @param array<string|int, mixed> $array
     */
    public static function inArrayRecursive($value, array $array, bool $strict = false): bool
    {
        if (in_array($value, $array, $strict)) {
            return true;
        }

        foreach ($array as $item) {
            if (is_iterable($item)) {
                $item = self::iterableToArray($item);

                if (self::inArrayRecursive($value, $item, $strict)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Re-coding of var_export for tidiness
     *
     * @param array<string|int, mixed> $array
     */
    public static function export(array $array): string
    {
        return self::exportLevel($array, 1);
    }

    /**
     * @param array<string|int, mixed> $array
     */
    private static function exportLevel(array $array, int $level): string
    {
        $output = '[' . "\n";

        $i = 0;
        $count = count($array);
        $isNumericIndex = true;

        foreach ($array as $key => $val) {
            if ($key !== $i++) {
                $isNumericIndex = false;
                break;
            }
        }

        $i = 0;

        foreach ($array as $key => $val) {
            $i++;
            $output .= str_repeat('    ', $level);

            if (!$isNumericIndex) {
                $output .= '\'' . addslashes((string)$key) . '\' => ';
            }

            if (is_object($val) || is_null($val)) {
                $output .= 'null';
            } elseif (is_array($val)) {
                $output .= self::exportLevel($val, $level + 1);
            } elseif (is_int($val) || is_float($val)) {
                $output .= $val;
            } elseif (is_bool($val)) {
                if ($val) {
                    $output .= 'true';
                } else {
                    $output .= 'false';
                }
            } else {
                $output .= '\'' . addslashes($val) . '\'';
            }

            if ($count > $i) {
                $output .= ',';
            }

            $output .= "\n";
        }

        $output .= str_repeat('    ', $level - 1) . ']';

        return $output;
    }
}
