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
 * @param mixed[]          $array
 * @param non-empty-string $key
 * @param mixed|null       $default
 * @param non-empty-string $delimiter
 *
 * @return array|mixed|null
 */
function dotGet(
    array $array,
           $key,
           $default = null,
           $delimiter = Dot::DEFAULT_DELIMITER
) {
    return Dot::get($array, $key, $default, $delimiter);
}

/**
 * Set the value in the array dictated by the dot notation key, if the path of the key does not exist it will be
 * created in the array.
 *
 * @param mixed[]          $array
 * @param non-empty-string $key
 * @param array|mixed|null $value
 * @param non-empty-string $delimiter
 *
 * @return void
 *
 * @throws InvalidArgumentException
 */
function dotSet(
    array &$array,
           $key,
           $value,
           $delimiter = Dot::DEFAULT_DELIMITER
) {
    Dot::set($array, $key, $value, $delimiter);
}

/**
 * Does the array have the passed dot notation key.
 *
 * @param mixed[]          $array
 * @param non-empty-string $key       Dot notation key
 * @param non-empty-string $delimiter
 *
 * @return bool
 *
 * @throws InvalidArgumentException if an invalid delimiter is used
 */
function dotHas(
    array $array,
           $key,
           $delimiter = Dot::DEFAULT_DELIMITER
) {
    return Dot::has($array, $key, $delimiter);
}

/**
 * Increment the value at the provided key position by the value passed in the incrementor (can be negative).  If
 * there is no value in the provided key position then the default value is used as an initializer (starting count)
 * for the value.
 *
 * @param mixed[]          $array
 * @param non-empty-string $key
 * @param int|float        $incrementor
 * @param int|float        $default
 * @param non-empty-string $delimiter
 *
 * @throws InvalidArgumentException if the value in the key position is not a numeric value
 *
 * @return int|float return the value in the key position after it has been incremented
 */
function dotIncrement(
    array &$array,
    $key,
    $incrementor,
    $default,
    $delimiter = Dot::DEFAULT_DELIMITER
) {
    return Dot::increment($array, $key, $incrementor, $default, $delimiter);
}

/**
 * Return the count of the value at the provided key position.  The method will return 0 on an empty array.
 * The $return defaults to return 0 if the value is not set or the key position is not an array.
 * It $return is set to Dot::COUNT_NEGATIVE_ON_NON_ARRAY the method will return -1 if the value is not set or the
 * key position is not an array.
 *
 * @param mixed[]          $array
 * @param non-empty-string $key
 * @param non-empty-string $delimiter
 * @param int              $return    defaults to returning 0 count on not set or not array, can be set to return -1
 *
 * @return int
 *
 * @throws InvalidArgumentException if an invalid delimiter is used
 */
function dotCount(
    array $array,
           $key,
           $delimiter = Dot::DEFAULT_DELIMITER,
           $return = Dot::ZERO_ON_NON_ARRAY
) {
    return Dot::count($array, $key, $delimiter, $return);
}

/**
 * Unset the provided key position in the array if it exists.
 *
 * @param mixed[]          $array
 * @param non-empty-string $key
 * @param non-empty-string $delimiter
 *
 * @return void
 *
 * @throws InvalidArgumentException
 */
function dotDelete(
    array &$array,
           $key,
           $delimiter = Dot::DEFAULT_DELIMITER
) {
    Dot::delete($array, $key, $delimiter);
}

/**
 * Append a value to the key position, if the value at the key position is not an array the result will be an
 * array with the existing value and new value.  If the key does not exist its full path will be set to an array
 * containing the value submitted.
 *
 * @param mixed[]          $array
 * @param non-empty-string $key
 * @param array|mixed|null $value
 * @param non-empty-string $delimiter
 *
 * @return void
 *
 * @throws InvalidArgumentException if an invalid deliminator is used
 */
function dotAppend(
    array &$array,
           $key,
           $value,
           $delimiter = Dot::DEFAULT_DELIMITER
) {
    Dot::append($array, $key, $value, $delimiter);
}

/**
 * Flatten a multidimensional array to a single dimension with dot keys => value.
 *
 * @param mixed[]          $array     The source array to flatten
 * @param non-empty-string $delimiter The delimiter to use between keys
 * @param string           $prepend   if there is any prepend string to the key sequence to use
 *
 * @return array<string, mixed> flattened single dimension array of the source array
 *
 * @throws InvalidArgumentException if an invalid delimiter is used
 */
function dotFlatten(
    array $array,
           $delimiter = Dot::DEFAULT_DELIMITER,
           $prepend = ''
) {
    return Dot::flatten($array, $delimiter, $prepend);
}
