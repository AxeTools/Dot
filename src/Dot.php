<?php

namespace AxeTools\Utilities\Dot;

/**
 * Utility for accessing and modifying arrays using dot notation with optional wildcard support.
 */
final class Dot {
    public const DEFAULT_DELIMITER = '.';
    public const DEFAULT_WILDCARD = '*';

    public const ZERO_ON_NON_ARRAY = 1;
    public const NEGATIVE_ON_NON_ARRAY = 2;

    private function __construct() {
    }

    /**
     * Return the value that the array has for the dot notation key, if there is no value to return the default is
     * returned.
     *
     * @param array<mixed>     $array
     * @param non-empty-string $key
     * @param mixed|null       $default   optional, the default value to return if there is no value set in the key
     *                                    position of the array
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     * @param non-empty-string $wildcard  optional, the delimiter used in the string key to break apart the key values
     *
     * @return array|mixed|null
     *
     * @since 1.0.0
     */
    public static function get(
        array $array,
        string $key,
        mixed $default = null,
        string $delimiter = self::DEFAULT_DELIMITER,
        string $wildcard = self::DEFAULT_WILDCARD,
    ): mixed {
        self::validateDelimiter($delimiter);

        $segments = explode($delimiter, $key);
        $results = [$array];

        foreach ($segments as $segment) {
            $next = [];

            foreach ($results as $current) {
                if (!is_array($current)) {
                    continue;
                }

                if ($segment === $wildcard) {
                    foreach ($current as $value) {
                        $next[] = $value;
                    }
                    continue;
                }

                if (array_key_exists($segment, $current)) {
                    $next[] = $current[$segment];
                }
            }

            if ([] === $next) {
                return $default;
            }

            $results = $next;
        }

        return 1 === count($results) ? $results[0] : $results;
    }

    /**
     * Does the array have the passed dot notation key.
     *
     * @param array<mixed>     $array
     * @param non-empty-string $key       a string representation of a nested key value delimited by '.' or the passed
     *                                    delimiters
     * @param non-empty-string $delimiter optional, the delimiter used in the string key to break apart the key values
     * @param non-empty-string $wildcard  optional, the delimiter used in the string key to break apart the key values
     */
    public static function has(
        array $array,
        string $key,
        string $delimiter = self::DEFAULT_DELIMITER,
        string $wildcard = self::DEFAULT_WILDCARD,
    ): bool {
        $sentinel = new \stdClass();

        $result = self::get($array, $key, $sentinel, $delimiter, $wildcard);

        return $result !== $sentinel;
    }

    /**
     * @param array<mixed>     $array
     * @param non-empty-string $key
     * @param non-empty-string $delimiter
     * @param non-empty-string $wildcard
     */
    public static function set(
        array &$array,
        string $key,
        mixed $value,
        string $delimiter = self::DEFAULT_DELIMITER,
        string $wildcard = self::DEFAULT_WILDCARD,
    ): void {
        self::validateDelimiter($delimiter);

        $segments = explode($delimiter, $key);
        self::setRecursive($array, $segments, $value, $wildcard);
    }

    /**
     * @param array<mixed> $current
     * @param list<string> $segments
     */
    private static function setRecursive(
        array &$current,
        array $segments,
        mixed $value,
        string $wildcard,
    ): void {
        $segment = array_shift($segments);

        if (null === $segment) {
            return;
        }

        if ([] === $segments) {
            if ($segment === $wildcard) {
                foreach ($current as &$item) {
                    $item = $value;
                }
            } else {
                $current[$segment] = $value;
            }

            return;
        }

        if ($segment === $wildcard) {
            foreach ($current as &$item) {
                if (is_array($item)) {
                    self::setRecursive($item, $segments, $value, $wildcard);
                }
            }

            return;
        }

        if (!isset($current[$segment]) || !is_array($current[$segment])) {
            $current[$segment] = [];
        }

        self::setRecursive($current[$segment], $segments, $value, $wildcard);
    }

    /**
     * @param array<mixed>     $array
     * @param non-empty-string $key
     * @param non-empty-string $delimiter
     * @param non-empty-string $wildcard
     */
    public static function increment(
        array &$array,
        string $key,
        float|int $increment = 1,
        float|int $default = 0,
        string $delimiter = self::DEFAULT_DELIMITER,
        string $wildcard = self::DEFAULT_WILDCARD,
    ): float|int {
        $value = self::get($array, $key, $default, $delimiter, $wildcard);

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException("Value at '{$key}' is not numeric");
        }

        $value += $increment;
        self::set($array, $key, $value, $delimiter, $wildcard);

        return $value;
    }

    /**
     * @param array<mixed>     $array
     * @param non-empty-string $key
     * @param non-empty-string $delimiter
     * @param non-empty-string $wildcard
     */
    public static function count(
        array $array,
        string $key,
        string $delimiter = self::DEFAULT_DELIMITER,
        int $return = self::ZERO_ON_NON_ARRAY,
        string $wildcard = self::DEFAULT_WILDCARD,
    ): int {
        $value = self::get($array, $key, null, $delimiter, $wildcard);

        if (!is_array($value)) {
            return self::NEGATIVE_ON_NON_ARRAY === $return ? -1 : 0;
        }

        return count($value);
    }

    /**
     * @param array<mixed>     $array
     * @param non-empty-string $key
     * @param non-empty-string $delimiter
     * @param non-empty-string $wildcard
     */
    public static function append(
        array &$array,
        string $key,
        mixed $value,
        string $delimiter = self::DEFAULT_DELIMITER,
        string $wildcard = self::DEFAULT_WILDCARD,
    ): void {
        $current = self::get($array, $key, [], $delimiter, $wildcard);

        if (!is_array($current)) {
            $current = [$current];
        }

        $values = is_array($value) ? $value : [$value];

        self::set($array, $key, array_merge($current, $values), $delimiter, $wildcard);
    }

    /**
     * @param array<mixed>     $array
     * @param non-empty-string $key
     * @param non-empty-string $delimiter
     * @param non-empty-string $wildcard
     */
    public static function delete(
        array &$array,
        string $key,
        string $delimiter = self::DEFAULT_DELIMITER,
        string $wildcard = self::DEFAULT_WILDCARD,
    ): void {
        self::validateDelimiter($delimiter);

        $segments = explode($delimiter, $key);
        self::deleteRecursive($array, $segments, $wildcard);
    }

    /**
     * @param array<mixed> $current
     * @param list<string> $segments
     */
    private static function deleteRecursive(
        array &$current,
        array $segments,
        string $wildcard,
    ): void {
        $segment = array_shift($segments);

        if (null === $segment) {
            return;
        }

        if ([] === $segments) {
            if ($segment === $wildcard) {
                foreach (array_keys($current) as $key) {
                    unset($current[$key]);
                }
            } else {
                unset($current[$segment]);
            }

            return;
        }

        if ($segment === $wildcard) {
            foreach ($current as &$item) {
                if (is_array($item)) {
                    self::deleteRecursive($item, $segments, $wildcard);
                }
            }

            return;
        }

        if (!isset($current[$segment]) || !is_array($current[$segment])) {
            return;
        }

        self::deleteRecursive($current[$segment], $segments, $wildcard);
    }

    /**
     * @param array<mixed>     $array
     * @param non-empty-string $delimiter
     *
     * @return array<string, mixed>
     */
    public static function flatten(
        array $array,
        string $delimiter = self::DEFAULT_DELIMITER,
        string $prefix = '',
    ): array {
        self::validateDelimiter($delimiter);

        $result = [];

        foreach ($array as $key => $value) {
            $newKey = '' === $prefix ? (string) $key : $prefix.$delimiter.$key;

            if (is_array($value) && [] !== $value) {
                $result += self::flatten($value, $delimiter, $newKey);
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    private static function validateDelimiter(string $delimiter): void {
        if ('' === $delimiter) {
            throw new \InvalidArgumentException('Delimiter cannot be empty');
        }
    }
}
