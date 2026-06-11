<?php

declare(strict_types=1);

namespace AxeTools\Utilities\Dot;

/**
 * Get and set and check values in an array using dot notation or any other optional key separator.
 */
final class Dot {
    public const DEFAULT_DELIMITER = '.';
    public const DEFAULT_WILDCARD = '*';

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
     * @param non-empty-string $wildcard
     *
     * @return mixed|null
     *
     * @throws \InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function get(
        array $searchArray,
        string $searchKey,
        mixed $default = null,
        string $delimiter = self::DEFAULT_DELIMITER,
        string $wildcard = self::DEFAULT_WILDCARD,
    ): mixed {
        self::validateDelimiter($delimiter);
        self::validateWildcard($wildcard, $delimiter);

        if (self::containsWildcard(
            $searchKey,
            $delimiter,
            $wildcard
        )) {
            $matches = self::getWildcardMatches(
                $searchArray,
                explode($delimiter, $searchKey),
                0,
                $wildcard
            );

            return [] === $matches
                ? $default
                : $matches;
        }

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
     * created in the array.  Take care when using the wildcard set behavior, leaf assignments may replace entire child
     * arrays.  Set with wildcard will only affect existing maths behind the wildcard.
     *
     * @param array<mixed>     $setArray
     * @param non-empty-string $setKey    a string representation of a nested key value delimited by '.' or the passed
     *                                    delimiters
     * @param array|mixed|null $value     The value to set in the array at the passed key location
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     * @param non-empty-string $wildcard
     *
     * @throws \InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function set(
        array &$setArray,
        string $setKey,
        mixed $value,
        string $delimiter = self::DEFAULT_DELIMITER,
        string $wildcard = self::DEFAULT_WILDCARD,
    ): void {
        self::validateDelimiter($delimiter);
        self::validateWildcard($wildcard, $delimiter);

        if (self::containsWildcard(
            $setKey,
            $delimiter,
            $wildcard
        )) {
            self::setWildcardRecursive(
                $setArray,
                explode($delimiter, $setKey),
                0,
                $value,
                $wildcard
            );

            return;
        }

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
     * Does the array contain the passed dot notation key?  If using a wildcard search, then the array has to contain at
     * least one key, but does not indicate that all matches exist.
     *
     * @param array<mixed>     $searchArray
     * @param non-empty-string $searchKey   a string representation of a nested key value delimited by '.' or the passed
     *                                      delimiters
     * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
     * @param non-empty-string $wildcard
     *
     * @throws \InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function has(
        array $searchArray,
        string $searchKey,
        string $delimiter = self::DEFAULT_DELIMITER,
        string $wildcard = self::DEFAULT_WILDCARD,
    ): bool {
        self::validateDelimiter($delimiter);
        self::validateWildcard($wildcard, $delimiter);

        if (self::containsWildcard(
            $searchKey,
            $delimiter,
            $wildcard
        )) {
            return [] !== self::getWildcardMatches(
                $searchArray,
                explode($delimiter, $searchKey),
                0,
                $wildcard
            );
        }

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
     * Increment the value at the provided key position by the value passed in the incrementor (can be negative). If
     * there is no value in the provided key position, then the initial value is used as an initializer (starting
     * count) for the value.
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
    public static function increment(
        array &$incrementArray,
        string $incrementKey,
        float|int $incrementor = 1,
        float|int $default = 0,
        string $delimiter = self::DEFAULT_DELIMITER,
    ): float|int {
        self::validateDelimiter($delimiter);

        if (self::containsWildcard($incrementKey, $delimiter, self::DEFAULT_WILDCARD)) {
            throw new \InvalidArgumentException('Wildcards are not supported by increment()');
        }

        $initialValue = self::get($incrementArray, $incrementKey, $default, $delimiter);

        if (!is_numeric($initialValue)) {
            throw new \InvalidArgumentException(sprintf('The value at key "%s" is not numeric', $incrementKey));
        }

        $result = $initialValue + $incrementor;

        self::set($incrementArray, $incrementKey, $result, $delimiter);

        return $result;
    }

    /**
     * Return the count of the value at the provided key position. The method will return 0 on an empty array.
     * The $return defaults to return 0 if the value is not set or the key position is not an array.
     * If $return is set to Dot::NEGATIVE_ON_NON_ARRAY the method will return -1 if the value is not set or the
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
    public static function count(
        array $countArray,
        string $countKey,
        string $delimiter = self::DEFAULT_DELIMITER,
        int $return = self::ZERO_ON_NON_ARRAY,
    ): int {
        self::validateDelimiter($delimiter);

        if (self::containsWildcard($countKey, $delimiter, self::DEFAULT_WILDCARD)) {
            throw new \InvalidArgumentException('Wildcards are not supported by count()');
        }

        $default = (self::NEGATIVE_ON_NON_ARRAY === $return) ? -1 : 0;
        $value = self::get($countArray, $countKey, null, $delimiter);

        return is_array($value) ? count($value) : $default;
    }

    /**
     * Append a value to the key position, if the value at the key position is not an array, the result will be an
     * array with the existing value and new value. If the key does not exist, its full path will be set to an array
     * containing the value submitted.
     *
     * @param array<mixed>     $appendArray
     * @param non-empty-string $appendKey   a string representation of a nested key value delimited by '.' or the passed
     *                                      delimiters
     * @param array|mixed|null $value
     * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
     *
     * @throws \InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    public static function append(
        array &$appendArray,
        string $appendKey,
        mixed $value,
        string $delimiter = self::DEFAULT_DELIMITER,
    ): void {
        self::validateDelimiter($delimiter);

        if (self::containsWildcard($appendKey, $delimiter, self::DEFAULT_WILDCARD)) {
            throw new \InvalidArgumentException('Wildcards are not supported by append()');
        }

        $current = self::get($appendArray, $appendKey, [], $delimiter);
        $current = is_array($current) ? $current : [$current];
        $value = is_array($value) ? $value : [$value];

        self::set($appendArray, $appendKey, array_merge($current, $value), $delimiter);
    }

    /**
     * Unset the provided key position in the array if it exists.
     *
     * @param array<mixed>     $deleteArray
     * @param non-empty-string $deleteKey   a string representation of a nested key value delimited by '.' or the passed
     *                                      delimiters
     * @param non-empty-string $delimiter   optional, the delimiter used in the string key to break apart the key values
     * @param non-empty-string $wildcard
     *
     * @since 1.0.0
     */
    public static function delete(
        array &$deleteArray,
        string $deleteKey,
        string $delimiter = self::DEFAULT_DELIMITER,
        string $wildcard = self::DEFAULT_WILDCARD,
    ): void {
        self::validateDelimiter($delimiter);
        self::validateWildcard($wildcard, $delimiter);

        if (self::containsWildcard(
            $deleteKey,
            $delimiter,
            $wildcard
        )) {
            self::deleteWildcardRecursive(
                $deleteArray,
                explode($delimiter, $deleteKey),
                0,
                $wildcard
            );

            return;
        }

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
    public static function flatten(
        array $array,
        string $delimiter = self::DEFAULT_DELIMITER,
        string $prepend = '',
    ): array {
        self::validateDelimiter($delimiter);

        $flattened = [];
        self::flattenRecursive($array, $flattened, $delimiter, $prepend);

        return $flattened;
    }

    /**
     * Validate that the delimiter provided is valid.
     *
     * @throws \InvalidArgumentException if an invalid delimiter is used
     *
     * @since 1.0.0
     */
    private static function validateDelimiter(string $delimiter): void {
        if ('' === $delimiter) {
            self::invalidDelimiterException('A string of length 0');
        }
    }

    /**
     * Common exception throwing for invalid delimiters.
     *
     * @throws \InvalidArgumentException
     *
     * @since 1.0.0
     */
    private static function invalidDelimiterException(string $message): never {
        throw new \InvalidArgumentException($message.' Delimiter is not valid');
    }

    /**
     * @param array<array-key,mixed> $current
     * @param mixed[]                $keys
     * @param non-empty-string       $wildcard
     */
    private static function deleteWildcardRecursive(
        array &$current,
        array $keys,
        int $position,
        string $wildcard,
    ): void {
        $segment = $keys[$position];

        if ($position === count($keys) - 1) {
            if ($segment === $wildcard) {
                foreach (array_keys($current) as $key) {
                    unset($current[$key]);
                }

                return;
            }

            unset($current[$segment]); // @phpstan-ignore-line

            return;
        }

        if ($segment === $wildcard) {
            foreach ($current as &$item) {
                if (is_array($item)) {
                    self::deleteWildcardRecursive(
                        $item,
                        $keys,
                        $position + 1,
                        $wildcard
                    );
                }
            }

            return;
        }

        if (
            isset($current[$segment]) // @phpstan-ignore-line
            && is_array($current[$segment]) // @phpstan-ignore-line
        ) {
            self::deleteWildcardRecursive(
                $current[$segment], // @phpstan-ignore-line
                $keys,
                $position + 1,
                $wildcard
            );
        }
    }

    /**
     * @param array<array-key,mixed> $current
     * @param non-empty-list<string> $keys
     * @param non-empty-string       $wildcard
     */
    private static function setWildcardRecursive(
        array &$current,
        array $keys,
        int $position,
        mixed $value,
        string $wildcard,
    ): void {
        $segment = $keys[$position];

        if ($position === count($keys) - 1) {
            if ($segment === $wildcard) {
                foreach ($current as &$item) {
                    $item = $value;
                }

                return;
            }

            if (array_key_exists($segment, $current)) {
                $current[$segment] = $value;
            }

            return;
        }

        if ($segment === $wildcard) {
            foreach ($current as &$item) {
                if (is_array($item)) {
                    self::setWildcardRecursive(
                        $item,
                        $keys,
                        $position + 1,
                        $value,
                        $wildcard
                    );
                }
            }

            return;
        }

        if (
            isset($current[$segment])
            && is_array($current[$segment])
        ) {
            self::setWildcardRecursive(
                $current[$segment],
                $keys,
                $position + 1,
                $value,
                $wildcard
            );
        }
    }

    /**
     * @param non-empty-list<string> $keys
     * @param non-empty-string       $wildcard
     *
     * @return mixed[]
     */
    private static function getWildcardMatches(
        mixed $current,
        array $keys,
        int $position,
        string $wildcard,
    ): array {
        if ($position >= count($keys)) {
            return [$current];
        }

        if (!is_array($current)) {
            return [];
        }

        $segment = $keys[$position];

        if ($segment === $wildcard) {
            $results = [];

            foreach ($current as $value) {
                foreach (self::getWildcardMatches($value,
                    $keys,
                    $position + 1,
                    $wildcard) as $match) {
                    $results[] = $match;
                }
            }

            return $results;
        }

        if (!array_key_exists($segment, $current)) {
            return [];
        }

        return self::getWildcardMatches(
            $current[$segment],
            $keys,
            $position + 1,
            $wildcard
        );
    }

    /**
     * @param non-empty-string $path
     * @param non-empty-string $delimiter
     * @param non-empty-string $wildcard
     */
    private static function containsWildcard(
        string $path,
        string $delimiter,
        string $wildcard,
    ): bool {
        return in_array(
            $wildcard,
            explode($delimiter, $path),
            true
        );
    }

    /**
     * @param non-empty-string $delimiter
     */
    private static function validateWildcard(
        string $wildcard,
        string $delimiter,
    ): void {
        if ('' === $wildcard) {
            throw new \InvalidArgumentException('Wildcard cannot be empty');
        }

        if ($wildcard === $delimiter) {
            throw new \InvalidArgumentException('Wildcard and delimiter must differ');
        }
    }

    /**
     * Internal flatten accumulator.
     *
     * @param mixed[] $array
     * @param mixed[] $result
     *
     * @since 1.0.0
     */
    private static function flattenRecursive(
        array $array,
        array &$result,
        string $delimiter,
        string $prepend,
    ): void {
        foreach ($array as $key => $value) {
            $newKey = $prepend.$key;

            if (is_array($value) && [] !== $value) {
                self::flattenRecursive(
                    $value,
                    $result,
                    $delimiter,
                    $newKey.$delimiter
                );
            } else {
                $result[$newKey] = $value;
            }
        }
    }
}
