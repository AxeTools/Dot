<?php

namespace AxeTools\Utilities\Dot;

/**
 * Get and set and check values in an array using dot notation or any other optional key separator.
 */
final class Dot {
    public const DEFAULT_DELIMITER = '.';

    public const ZERO_ON_NON_ARRAY = 1;
    public const NEGATIVE_ON_NON_ARRAY = 2;

    /**
     * This class is a static class and should not be instantiated.
     */
    private function __construct() {
    }

    /**
     * Return the value that the array has for the dot notation key, if there is no value to return, the default is
     * returned.
     *
     * @param array<mixed>     $searchArray
     * @param non-empty-string $searchKey   a string representation of a nested key value delimited by '.' or the passed
     *                                      delimiters
     * @param mixed|null       $default     optional, the default value to return if there is no value set in the key
     *                                      position of the array
     * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
     *
     * @return mixed|null
     *
     * @throws \InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function get(array $searchArray, string $searchKey, mixed $default = null, string $delimiter = self::DEFAULT_DELIMITER): mixed {
        self::validateDelimiter($delimiter);

        $keys = explode($delimiter, $searchKey);
        $current = $searchArray;

        foreach ($keys as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return $default;
            }

            $current = $current[$key];
        }

        return $current;
    }

    /**
     * Set the value in the array dictated by the dot notation key, if the path of the key does not exist, it will be
     * created in the array.
     *
     * @param array<mixed>     $setArray
     * @param string           $setKey    a string representation of a nested key value delimited by '.' or the passed
     *                                    delimiters
     * @param array|mixed|null $value     The value to set in the array at the passed key location
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     *
     * @throws \InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function set(array &$setArray, string $setKey, mixed $value, string $delimiter = self::DEFAULT_DELIMITER): void {
        self::validateDelimiter($delimiter);

        $keys = explode($delimiter, $setKey);
        $current = &$setArray;
        $last = array_pop($keys);

        foreach ($keys as $key) {
            if (!isset($current[$key]) || !is_array($current[$key])) {
                $current[$key] = [];
            }

            $current = &$current[$key];
        }

        $current[$last] = $value;
    }

    /**
     * Does the array contain the passed dot notation key?
     *
     * @param array<mixed>     $searchArray
     * @param non-empty-string $searchKey   a string representation of a nested key value delimited by '.' or the passed
     *                                      delimiters
     * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
     *
     * @throws \InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function has(array $searchArray, string $searchKey, string $delimiter = self::DEFAULT_DELIMITER): bool {
        self::validateDelimiter($delimiter);

        $keys = explode($delimiter, $searchKey);
        $current = $searchArray;

        foreach ($keys as $key) {
            if (!is_array($current) || !array_key_exists($key, $current)) {
                return false;
            }

            $current = $current[$key];
        }

        return true;
    }

    /**
     * Increment the value at the provided key position by the value passed in the incrementor (can be negative).  If
     * there is no value in the provided key position, then the initial value is used as an initializer (starting
     * count)
     * for the value.
     *
     * @param array<mixed>     $incrementArray
     * @param non-empty-string $incrementKey   a string representation of a nested key value delimited by '.' or the
     *                                         passed delimiters
     * @param float|int        $incrementor    optional, incrementing amount, defaults to +1
     * @param float|int        $default        optional, default amount if the key location has no initial value
     * @param non-empty-string $delimiter      optional, the delimiter used in the string key to break apart the key values
     *
     * @return float|int return the value in the key position after it has been incremented
     *
     * @since 1.0.0
     */
    public static function increment(array &$incrementArray, string $incrementKey, float|int $incrementor = 1, float|int $default = 0, string $delimiter = self::DEFAULT_DELIMITER): float|int {
        self::validateDelimiter($delimiter);

        $initial_value = self::get($incrementArray, $incrementKey, $default, $delimiter);

        if (!is_numeric($initial_value)) {
            throw new \InvalidArgumentException("The value at the key position '{$incrementKey}' is not a numeric value");
        }

        $result = $initial_value + $incrementor;
        self::set($incrementArray, $incrementKey, $result, $delimiter);

        return $result;
    }

    /**
     * Return the count of the value at the provided key position.  The method will return 0 on an empty array.
     * The $return defaults to return 0 if the value is not set or the key position is not an array.
     * It $return is set to Dot::COUNT_NEGATIVE_ON_NON_ARRAY the method will return -1 if the value is not set or the
     * key position is not an array.
     *
     * @param array<mixed>     $countArray
     * @param non-empty-string $countKey   a string representation of a nested key value delimited by '.' or the passed
     *                                     delimiters
     * @param non-empty-string $delimiter  optional, the delimiter used in the string key to break apart the key values
     * @param int              $return     defaults to returning 0 count on not set or not array, can be set to return -1
     *
     * @throws \InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function count(array $countArray, string $countKey, string $delimiter = self::DEFAULT_DELIMITER, int $return = self::ZERO_ON_NON_ARRAY): int {
        self::validateDelimiter($delimiter);
        $default = (self::NEGATIVE_ON_NON_ARRAY === $return) ? -1 : 0;
        $position = self::get($countArray, $countKey, '', $delimiter);

        return is_array($position) ? count($position) : $default;
    }

    /**
     * Append a value to the key position, if the value at the key position is not an array, the result will be an
     * array with the existing value and new value.  If the key does not exist, its full path will be set to an array
     * containing the value submitted.
     *
     * @param array<mixed>     $appendArray
     * @param non-empty-string $appendKey   a string representation of a nested key value delimited by '.' or the passed
     *                                      delimiters
     * @param array|mixed|null $value
     * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
     *
     * @throws \InvalidArgumentException if an invalid deliminator is used
     *
     * @since 1.0.0
     */
    public static function append(array &$appendArray, string $appendKey, mixed $value, string $delimiter = self::DEFAULT_DELIMITER): void {
        self::validateDelimiter($delimiter);
        $current = self::get($appendArray, $appendKey, [], $delimiter);
        $current = (is_array($current)) ? $current : [$current];
        $value = (is_array($value)) ? $value : [$value];

        self::set($appendArray, $appendKey, array_merge($current, $value), $delimiter);
    }

    /**
     * Unset the provided key position in the array if it exists.
     *
     * @param array<mixed>     $deleteArray
     * @param non-empty-string $deleteKey   a string representation of a nested key value delimited by '.' or the passed
     *                                      delimiters
     * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
     *
     * @since 1.0.0
     */
    public static function delete(array &$deleteArray, string $deleteKey, string $delimiter = self::DEFAULT_DELIMITER): void {
        self::validateDelimiter($delimiter);

        $keys = explode($delimiter, $deleteKey);
        $final = array_pop($keys);

        $current = &$deleteArray;

        foreach ($keys as $key) {
            if (!isset($current[$key]) || !is_array($current[$key])) {
                return;
            }

            $current = &$current[$key];
        }

        if (array_key_exists($final, $current)) {
            unset($current[$final]);
        }
    }

    /**
     * Flatten a multidimensional array to a single dimension with dot keys => value.
     *
     * @param array<mixed>     $array     The source array to flatten
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     * @param string           $prepend   if there is any prepended string to the key sequence to use
     *
     * @return array<string, mixed> flattened single-dimension array of the source array
     *
     * @throws \InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function flatten(array $array, string $delimiter = self::DEFAULT_DELIMITER, string $prepend = ''): array {
        self::validateDelimiter($delimiter);

        $flattened = [];
        self::flattenRecursive($array, $flattened, $delimiter, $prepend);

        return $flattened;
    }

    /**
     * Validate that the deliminator provided is valid.
     *
     * @throws \InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    private static function validateDelimiter(string $delimiter): void {
        if ('' === $delimiter) {
            self::InvalidDelimiterException('A string of length 0');
        }
    }

    /**
     * Common exception throwing for invalid delimiters.
     *
     * @throws \InvalidArgumentException
     *
     * @since 1.0.0
     */
    private static function InvalidDelimiterException(string $message): void {
        throw new \InvalidArgumentException($message.' Delimiter is not valid');
    }

    /**
     * Internal flatten accumulator.
     *
     * @param mixed[] $array
     * @param mixed[] $result
     * @param string  $delimiter
     * @param string  $prepend
     *
     * @return void
     */
    private static function flattenRecursive(array $array, array &$result, $delimiter, $prepend) {
        foreach ($array as $key => $value) {
            $newKey = $prepend.$key;

            if (is_array($value) && !empty($value)) {
                self::flattenRecursive($value, $result, $delimiter, $newKey.$delimiter);
            } else {
                $result[$newKey] = $value;
            }
        }
    }
}
