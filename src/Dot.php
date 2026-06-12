<?php

namespace AxeTools\Utilities\Dot;

use InvalidArgumentException;

/**
 * Get and set and check values in an array using dot notation or any other optional key separator.
 */
final class Dot {
    const DEFAULT_DELIMITER = '.';
    const DEFAULT_WILDCARD = '*';

    const ZERO_ON_NON_ARRAY = 1;
    const NEGATIVE_ON_NON_ARRAY = 2;

    /**
     * This class is a static class and should not be instantiated.
     */
    private function __construct() {}

    /**
     * Return the value that the array has for the dot notation key, if there is no value to return the default is returned.
     *
     * @param mixed[] $searchArray
     * @param non-empty-string $searchKey a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param mixed|null $default optional, the default value to return if there is no value set in the key position of the array
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     *
     * @return array|mixed|null
     *
     * @throws InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function get(array $searchArray, $searchKey, $default = null, $delimiter = self::DEFAULT_DELIMITER, $wildcard = self::DEFAULT_WILDCARD) {
        self::validateDelimiter($delimiter);
        self::validateWildcard($wildcard);
        self::validateDelimiterWildcardConflict($delimiter, $wildcard);

        //if (strpos($searchKey, $wildcard) !== false) {
        //    return self::wildcardGet($searchArray, explode($delimiter, $searchKey), $default, $wildcard);
        //}

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
     * Set the value in the array dictated by the dot notation key, if the path of the key does not exist it will be
     * created in the array.
     *
     * @param mixed[] $setArray
     * @param non-empty-string $setKey a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param array|mixed|null $value The value to set in the array at the passed key location
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     *
     * @return void
     *
     * @throws InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function set(array &$setArray, $setKey, $value, $delimiter = self::DEFAULT_DELIMITER, $wildcard = self::DEFAULT_WILDCARD) {
        self::validateDelimiter($delimiter);
        self::validateWildcard($wildcard);
        self::validateDelimiterWildcardConflict($delimiter, $wildcard);

        //if (strpos($setKey, $wildcard) !== false) {
        //    self::wildcardSet($setArray, explode($delimiter, $setKey), $value, $wildcard);
        //    return;
        //}

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
     * @param mixed[] $searchArray
     * @param non-empty-string $searchKey a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     *
     * @return bool
     *
     * @throws InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function has(array $searchArray, $searchKey, $delimiter = self::DEFAULT_DELIMITER, $wildcard = self::DEFAULT_WILDCARD) {
        self::validateDelimiter($delimiter);
        self::validateWildcard($wildcard);
        self::validateDelimiterWildcardConflict($delimiter, $wildcard);

        //if (strpos($searchKey, $wildcard) !== false) {
        //    return self::wildcardHas($searchArray, explode($delimiter, $searchKey), $wildcard);
        //}

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
     * there is no value in the provided key position then the initial value is used as an initializer (starting count)
     * for the value.
     *
     * @param mixed[] $incrementArray
     * @param non-empty-string $incrementKey a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param int|float $incrementor optional, incrementing amount, defaults to +1
     * @param int|float $default optional, default amount if the key location has no initial value
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     *
     * @return int|float return the value in the key position after it has been incremented
     *
     * @throws InvalidArgumentException if the value in the key position is not a numeric value
     *
     * @since 1.0.0
     */
    public static function increment(array &$incrementArray, $incrementKey, $incrementor = 1, $default = 0, $delimiter = self::DEFAULT_DELIMITER) {
        self::validateDelimiter($delimiter);

        //if (strpos($incrementKey, self::DEFAULT_WILDCARD) !== false) {
        //    throw new InvalidArgumentException('Wildcard not supported in increment()');
        //}

        if (!is_numeric($incrementor)) {
            throw new InvalidArgumentException('The provided incrementor is not a numeric value');
        }

        $initial_value = self::get($incrementArray, $incrementKey, $default, $delimiter);

        if (!is_numeric($initial_value)) {
            throw new InvalidArgumentException("The value at the key position '{$incrementKey}' is not a numeric value");
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
     * @param mixed[] $countArray
     * @param non-empty-string $countKey a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     * @param int $return defaults to returning 0 count on not set or not array, can be set to return -1
     *
     * @return int
     *
     * @throws InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function count(array $countArray, $countKey, $delimiter = self::DEFAULT_DELIMITER, $return = self::ZERO_ON_NON_ARRAY) {
        self::validateDelimiter($delimiter);

        //if (strpos($countKey, self::DEFAULT_WILDCARD) !== false) {
        //    throw new InvalidArgumentException('Wildcard not supported in count()');
        // }

        $default = (self::NEGATIVE_ON_NON_ARRAY === $return) ? -1 : 0;
        $position = self::get($countArray, $countKey, '', $delimiter);

        return is_array($position) ? count($position) : $default;
    }

    /**
     * Append a value to the key position, if the value at the key position is not an array the result will be an
     * array with the existing value and new value.  If the key does not exist its full path will be set to an array
     * containing the value submitted.
     *
     * @param mixed[] $appendArray
     * @param non-empty-string $appendKey a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param array|mixed|null $value
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     *
     * @return void
     *
     * @throws InvalidArgumentException if an invalid deliminator is used
     *
     * @since 1.0.0
     */
    public static function append(array &$appendArray, $appendKey, $value, $delimiter = self::DEFAULT_DELIMITER) {
        self::validateDelimiter($delimiter);

//        if (strpos($appendKey, self::DEFAULT_WILDCARD) !== false) {
//            throw new InvalidArgumentException('Wildcard not supported in append()');
//        }

        $current = self::get($appendArray, $appendKey, [], $delimiter);
        $current = (is_array($current)) ? $current : [$current];
        $value = (is_array($value)) ? $value : [$value];

        self::set($appendArray, $appendKey, array_merge($current, $value), $delimiter);
    }

    /**
     * Unset the provided key position in the array if it exists.
     *
     * @param mixed[] $deleteArray
     * @param non-empty-string $deleteKey a string representation of a nested key value delimited by '.' or the passed delimiters
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     *
     * @return void
     *
     * @since 1.0.0
     */
    public static function delete(array &$deleteArray, $deleteKey, $delimiter = self::DEFAULT_DELIMITER, $wildcard = self::DEFAULT_WILDCARD) {
        self::validateDelimiter($delimiter);
        self::validateWildcard($wildcard);
        self::validateDelimiterWildcardConflict($delimiter, $wildcard);

//        if (strpos($deleteKey, $wildcard) !== false) {
//            self::wildcardDelete($deleteArray, explode($delimiter, $deleteKey), $wildcard);
//            return;
//        }

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
     * @param mixed[] $array The source array to flatten
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     * @param string $prepend if there is any prepend string to the key sequence to use
     *
     * @return array<string, mixed> flattened single dimension array of the source array
     * @throws InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     *
     */
    public static function flatten(array $array, $delimiter = self::DEFAULT_DELIMITER, $prepend = '') {
        self::validateDelimiter($delimiter);

//        if (strpos($prepend, self::DEFAULT_WILDCARD) !== false) {
//            throw new InvalidArgumentException('Wildcard not supported in flatten()');
//        }

        $flattened = [];
        self::flattenRecursive($array, $flattened, $delimiter, $prepend);

        return $flattened;
    }

    private static function wildcardGet($array, $segments, $default, $wildcard, &$results = null) {
        if ($results === null) {
            $results = [];
        }

        $segment = array_shift($segments);

        if ($segment === $wildcard) {
            foreach ($array as $value) {
                if ($segments) {
                    if (is_array($value)) {
                        self::wildcardGet($value, $segments, $default, $wildcard, $results);
                    }
                } else {
                    $results[] = $value;
                }
            }
        } else {
            if (!array_key_exists($segment, $array)) {
                return $results;
            }

            if ($segments) {
                if (!is_array($array[$segment])) {
                    return $results;
                }

                self::wildcardGet($array[$segment], $segments, $default, $wildcard, $results);
            } else {
                $results[] = $array[$segment];
            }
        }

        return empty($results) ? [$default] : $results;
    }

    private static function wildcardSet(array &$array, $segments, $value, $wildcard) {
        $segment = array_shift($segments);

        if ($segment === $wildcard) {
            foreach ($array as &$item) {
                if (!is_array($item)) continue;
                if ($segments) {
                    self::wildcardSet($item, $segments, $value, $wildcard);
                }
            }
        } else {
            if ($segments) {
                if (!isset($array[$segment]) || !is_array($array[$segment])) return;
                self::wildcardSet($array[$segment], $segments, $value, $wildcard);
            } else {
                $array[$segment] = $value;
            }
        }
    }

    private static function wildcardHas($array, $segments, $wildcard) {
        $segment = array_shift($segments);

        if ($segment === $wildcard) {
            foreach ($array as $value) {
                if ($segments) {
                    if (is_array($value) && self::wildcardHas($value, $segments, $wildcard)) {
                        return true;
                    }
                } else {
                    return true;
                }
            }
            return false;
        }

        if (!array_key_exists($segment, $array)) return false;

        if ($segments) {
            if (!is_array($array[$segment])) return false;
            return self::wildcardHas($array[$segment], $segments, $wildcard);
        }

        return true;
    }

    private static function wildcardDelete(array &$array, $segments, $wildcard) {
        $segment = array_shift($segments);

        if ($segment === $wildcard) {
            foreach ($array as &$value) {
                if (is_array($value)) {
                    self::wildcardDelete($value, $segments, $wildcard);
                }
            }
        } else {
            if ($segments) {
                if (isset($array[$segment]) && is_array($array[$segment])) {
                    self::wildcardDelete($array[$segment], $segments, $wildcard);
                }
            } else {
                unset($array[$segment]);
            }
        }
    }

    private static function validateWildcard($wildcard) {
        if (is_null($wildcard)) self::InvalidDelimiterException('A Null');
        if (is_array($wildcard)) self::InvalidDelimiterException('An Array');
        if ($wildcard === '') self::InvalidDelimiterException('A string of length 0');
    }

    /**
     * Validate that delimiter and wildcard are not the same.
     *
     * @param string $delimiter
     * @param string $wildcard
     *
     * @return void
     * @throws InvalidArgumentException
     *
     */
    private static function validateDelimiterWildcardConflict($delimiter, $wildcard) {
        if ($delimiter === $wildcard) {
            trigger_error('Delimiter and wildcard cannot be the same value, change delimiter or wildcard. This will throw InvalidArgumentException in future releases', E_USER_WARNING);
            // throw new InvalidArgumentException('Delimiter and wildcard cannot be the same value');
        }
    }


    /**
     * Validate that the deliminator provided is valid.
     *
     * @param mixed $delimiter
     *
     * @return void
     * @throws InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     *
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
     * @return void
     * @throws InvalidArgumentException
     *
     * @since 1.0.0
     *
     */
    private static function InvalidDelimiterException($message) {
        throw new InvalidArgumentException($message . ' Delimiter is not valid');
    }

    /**
     * Internal flatten accumulator.
     *
     * @param mixed[] $array
     * @param mixed[] $result
     * @param string $delimiter
     * @param string $prepend
     *
     * @return void
     */
    private static function flattenRecursive(array $array, array &$result, $delimiter, $prepend) {
        foreach ($array as $key => $value) {
            $newKey = $prepend . $key;

            if (is_array($value) && !empty($value)) {
                self::flattenRecursive($value, $result, $delimiter, $newKey . $delimiter);
            } else {
                $result[$newKey] = $value;
            }
        }
    }
}
