<?php

namespace AxeTools\Utilities\Dot;

use InvalidArgumentException;

/**
 * Get and set and check values in an array using dot notation or any other optional key separator.
 */
final class Dot {
    const DEFAULT_DELIMITER = '.';

    const ZERO_ON_NON_ARRAY = 1;
    const NEGATIVE_ON_NON_ARRAY = 2;

    /**
     * This class is a static class and should not be instantiated.
     */
    private function __construct() {
    }

    /**
     * Return the value that the array has for the dot notation key, if there is no value to return the default is returned.
     *
     * @param mixed[]          $searchArray
     * @param non-empty-string $searchKey   a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param mixed|null       $default     optional, the default value to return if there is no value set in the key position of the array
     * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
     *
     * @return array|mixed|null
     *
     * @throws InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function get(array $searchArray, $searchKey, $default = null, $delimiter = self::DEFAULT_DELIMITER) {
        self::validateDelimiter($delimiter);
        $keys = explode($delimiter, $searchKey);
        $key_pos = array_shift($keys);

        if (array_key_exists($key_pos, $searchArray)) {
            if (is_array($searchArray[$key_pos]) && count($keys)) {
                return self::get($searchArray[$key_pos], implode($delimiter, $keys), $default, $delimiter);
            } else {
                if (count($keys)) {
                    return $default;
                }

                return $searchArray[$key_pos];
            }
        } else {
            return $default;
        }
    }

    /**
     * Set the value in the array dictated by the dot notation key, if the path of the key does not exist it will be
     * created in the array.
     *
     * @param mixed[]          $setArray
     * @param non-empty-string $setKey    a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param array|mixed|null $value     The value to set in the array at the passed key location
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     *
     * @return void
     *
     * @throws InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function set(array &$setArray, $setKey, $value, $delimiter = self::DEFAULT_DELIMITER) {
        self::validateDelimiter($delimiter);
        $keys = explode($delimiter, $setKey);
        $key_pos = array_shift($keys);

        if (count($keys)) {
            if (!array_key_exists($key_pos, $setArray) || !is_array($setArray[$key_pos])) {
                $setArray[$key_pos] = [];
            }
            self::set($setArray[$key_pos], implode($delimiter, $keys), $value, $delimiter);
        } else {
            $setArray[$key_pos] = $value;
        }
    }

    /**
     * Does the array have the passed dot notation key.
     *
     * @param mixed[]          $searchArray
     * @param non-empty-string $searchKey   a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
     *
     * @return bool
     *
     * @throws InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function has(array $searchArray, $searchKey, $delimiter = self::DEFAULT_DELIMITER) {
        self::validateDelimiter($delimiter);
        $v = self::get($searchArray, $searchKey, "\0\0", $delimiter);

        return "\0\0" !== $v; // if the default value is returned then the key was not found
    }

    /**
     * Increment the value at the provided key position by the value passed in the incrementor (can be negative).  If
     * there is no value in the provided key position then the initial value is used as an initializer (starting count)
     * for the value.
     *
     * @param mixed[]          $incrementArray
     * @param non-empty-string $incrementKey   a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param int|float        $incrementor    optional, incrementing amount, defaults to +1
     * @param int|float        $default        optional, default amount if the key location has no initial value
     * @param non-empty-string $delimiter      optional, the delimiter used in the string key to break apart the key values
     *
     * @return int|float return the value in the key position after it has been incremented
     *
     * @throws InvalidArgumentException if the value in the key position is not a numeric value
     *
     * @since 1.0.0
     */
    public static function increment(array &$incrementArray, $incrementKey, $incrementor = 1, $default = 0, $delimiter = self::DEFAULT_DELIMITER) {
        self::validateDelimiter($delimiter);
        if (!is_numeric($incrementor)) {
            throw new InvalidArgumentException('The provided incrementor is not a numeric value');
        }
        $initial_value = self::get($incrementArray, $incrementKey, $default, $delimiter);
        if (!is_numeric($initial_value)) {
            throw new InvalidArgumentException("The value at the key position '{$incrementKey}' is not a numeric value");
        }
        self::set($incrementArray, $incrementKey,
            $initial_value + $incrementor, $delimiter);

        return self::get($incrementArray, $incrementKey, null, $delimiter);
    }

    /**
     * Return the count of the value at the provided key position.  The method will return 0 on an empty array.
     * The $return defaults to return 0 if the value is not set or the key position is not an array.
     * It $return is set to Dot::COUNT_NEGATIVE_ON_NON_ARRAY the method will return -1 if the value is not set or the
     * key position is not an array.
     *
     * @param mixed[]          $countArray
     * @param non-empty-string $countKey   a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param non-empty-string $delimiter  optional, the delimiter used in the string key to break apart the key values
     * @param int              $return     defaults to returning 0 count on not set or not array, can be set to return -1
     *
     * @return int
     *
     * @throws InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function count(array $countArray, $countKey, $delimiter = self::DEFAULT_DELIMITER, $return = self::ZERO_ON_NON_ARRAY) {
        self::validateDelimiter($delimiter);
        $default = (self::NEGATIVE_ON_NON_ARRAY === $return) ? -1 : 0;
        $position = self::get($countArray, $countKey, '', $delimiter);

        return is_array($position) ? count($position) : $default;
    }

    /**
     * Append a value to the key position, if the value at the key position is not an array the result will be an
     * array with the existing value and new value.  If the key does not exist its full path will be set to an array
     * containing the value submitted.
     *
     * @param mixed[]          $appendArray
     * @param non-empty-string $appendKey   a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param array|mixed|null $value
     * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
     *
     * @return void
     *
     * @throws InvalidArgumentException if an invalid deliminator is used
     *
     * @since 1.0.0
     */
    public static function append(array &$appendArray, $appendKey, $value, $delimiter = self::DEFAULT_DELIMITER) {
        self::validateDelimiter($delimiter);
        $current = self::get($appendArray, $appendKey, [], $delimiter);
        $current = (is_array($current)) ? $current : [$current];
        $value = (is_array($value)) ? $value : [$value];
        self::set($appendArray, $appendKey, array_merge($current, $value), $delimiter);
    }

    /**
     * Unset the provided key position in the array if it exists.
     *
     * @param mixed[]          $deleteArray
     * @param non-empty-string $deleteKey   a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
     *
     * @return void
     *
     *@since 1.0.0
     */
    public static function delete(array &$deleteArray, $deleteKey, $delimiter = self::DEFAULT_DELIMITER) {
        self::validateDelimiter($delimiter);

        if (!self::has($deleteArray, $deleteKey)) {
            return;
        }

        $keys = explode($delimiter, $deleteKey);
        $final = array_pop($keys);
        $current = &$deleteArray;
        foreach ($keys as $_key) {
            if (is_array($current[$_key])) {
                $current = &$current[$_key];
            }
        }

        unset($current[$final]);
    }

    /**
     * Flatten a multidimensional array to a single dimension with dot keys => value.
     *
     * @param mixed[]          $array     The source array to flatten
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     * @param string           $prepend   if there is any prepend string to the key sequence to use
     *
     * @throws InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     *
     * @return array<string, mixed> flattened single dimension array of the source array
     */
    public static function flatten(array $array, $delimiter = self::DEFAULT_DELIMITER, $prepend = '') {
        self::validateDelimiter($delimiter);
        $flattened = [];
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $flattened = array_merge($flattened, self::flatten($value, $delimiter, $prepend.$key.$delimiter));
            } else {
                $flattened[$prepend.$key] = $value;
            }
        }

        return $flattened;
    }

    /**
     * Validate that the deliminator provided is valid.
     *
     * @param mixed $delimiter
     *
     * @throws InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     *
     * @return void
     */
    private static function validateDelimiter($delimiter) {
        if (is_null($delimiter)) {
            self::InvalidDelimiterException('A Null');
        }
        if (is_array($delimiter)) {
            self::InvalidDelimiterException('An Array');
        }
        if ('' === $delimiter) {
            self::InvalidDelimiterException('A string of length 0');
        }
    }

    /**
     * Common exception throwing for invalid delimiters.
     *
     * @param string $message
     *
     * @throws InvalidArgumentException
     *
     * @since 1.0.0
     *
     * @return void
     */
    private static function InvalidDelimiterException($message) {
        throw new InvalidArgumentException($message.' Delimiter is not valid');
    }
}
