<?php

/**
 * The functions are direct wrappers of the Dot class's static methods.  This allows for direct easy access to the
 * functionality without needing an additional use statement.
 */

use AxeTools\Utilities\Dot\Dot;

/**
 * Return the value that the array has for the dot notation key, if there is no value to return the default is
 * returned.
 *
 * @param array<mixed>     $searchArray
 * @param non-empty-string $searchKey   a string representation of a nested key value delimited by '.' or the passed
 *                                      delimiters
 * @param mixed|null       $default     optional, the default value to return if there is no value set in the key position
 *                                      of the array
 * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
 *
 * @return array|mixed|null
 *
 * @throws InvalidArgumentException if an invalid delimiter is used
 *
 * @since 1.0.0
 */
function dotGet(
    array $searchArray, string $searchKey, mixed $default = null, string $delimiter = Dot::DEFAULT_DELIMITER,
): mixed {
    return Dot::get($searchArray, $searchKey, $default, $delimiter);
}

/**
 * Set the value in the array dictated by the dot notation key, if the path of the key does not exist it will be
 * created in the array.
 *
 * @param array<mixed>     $setArray
 * @param string           $setKey    a string representation of a nested key value delimited by '.' or the passed
 *                                    delimiters
 * @param array|mixed|null $value     The value to set in the array at the passed key location
 * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
 *
 * @throws InvalidArgumentException if an invalid delimiter is used
 *
 * @since 1.0.0
 */
function dotSet(array &$setArray, string $setKey, mixed $value, string $delimiter = Dot::DEFAULT_DELIMITER): void {
    Dot::set($setArray, $setKey, $value, $delimiter);
}

/**
 * Does the array have the passed dot notation key.
 *
 * @param array<mixed>     $searchArray
 * @param non-empty-string $searchKey   a string representation of a nested key value delimited by '.' or the passed
 *                                      delimiters
 * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
 *
 * @throws InvalidArgumentException if an invalid delimiter is used
 *
 * @since 1.0.0
 */
function dotHas(array $searchArray, string $searchKey, string $delimiter = Dot::DEFAULT_DELIMITER): bool {
    return Dot::has($searchArray, $searchKey, $delimiter);
}

/**
 * Increment the value at the provided key position by the value passed in the incrementor (can be negative).  If
 * there is no value in the provided key position then the initial value is used as an initializer (starting count)
 * for the value.
 *
 * @param array<mixed>     $incrementArray
 * @param non-empty-string $incrementKey   a string representation of a nested key value delimited by '.' or the passed
 *                                         delimiters
 * @param float|int        $incrementor    optional, incrementing amount, defaults to +1
 * @param float|int        $default        optional, default amount if the key location has no initial value
 * @param non-empty-string $delimiter      optional, the delimiter used in the string key to break apart the key values
 *
 * @return int|float return the value in the key position after it has been incremented
 *
 * @throws InvalidArgumentException if the value in the key position is not a numeric value
 *
 * @since 1.0.0
 */
function dotIncrement(array &$incrementArray, string $incrementKey, float|int $incrementor = 1, float|int $default = 0, string $delimiter = Dot::DEFAULT_DELIMITER): float|int {
    return Dot::increment($incrementArray, $incrementKey, $incrementor, $default, $delimiter);
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
 * @throws InvalidArgumentException if an invalid delimiter is used
 *
 * @since 1.0.0
 */
function dotCount(array $countArray, string $countKey, string $delimiter = Dot::DEFAULT_DELIMITER, int $return = Dot::ZERO_ON_NON_ARRAY): int {
    return Dot::count($countArray, $countKey, $delimiter, $return);
}

/**
 * Unset the provided key position in the array if it exists.
 *
 * @param array<mixed>     $array
 * @param non-empty-string $key
 * @param non-empty-string $delimiter
 *
 * @throws InvalidArgumentException
 */
function dotDelete(
    array &$array,
    string $key,
    string $delimiter = Dot::DEFAULT_DELIMITER,
): void {
    Dot::delete($array, $key, $delimiter);
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
 * @throws InvalidArgumentException if an invalid deliminator is used
 *
 * @since 1.0.0
 */
function dotAppend(array &$appendArray, string $appendKey, mixed $value, string $delimiter = Dot::DEFAULT_DELIMITER): void {
    Dot::append($appendArray, $appendKey, $value, $delimiter);
}

/**
 * Flatten a multidimensional array to a single dimension with dot keys => value.
 *
 * @param array<mixed>     $array     The source array to flatten
 * @param non-empty-string $delimiter The delimiter to use between keys
 * @param string           $prepend   if there is any prepended string to the key sequence to use
 *
 * @return array<string, mixed> flattened single-dimension array of the source array
 *
 * @throws InvalidArgumentException if an invalid delimiter is used
 */
function dotFlatten(
    array $array,
    string $delimiter = Dot::DEFAULT_DELIMITER,
    string $prepend = '',
): array {
    return Dot::flatten($array, $delimiter, $prepend);
}
