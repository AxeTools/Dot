<?php

namespace Tests\AxeTools\Utilities\Dot;

use AxeTools\Utilities\Dot\Dot;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class CountTest extends DotBase {
    /**
     * @param array<mixed>     $test
     * @param non-empty-string $key
     * @param non-empty-string $delimiter
     *
     * @return void
     */
    #[Test]
    #[DataProvider('dotCountDataProvider')]
    public function dotCount(array $test, string $key, int $expected, string $delimiter = Dot::DEFAULT_DELIMITER, int $return = Dot::ZERO_ON_NON_ARRAY) {
        $actual = Dot::count($test, $key, $delimiter, $return);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param array<mixed>     $test
     * @param non-empty-string $key
     * @param non-empty-string $delimiter
     *
     * @return void
     */
    #[Test]
    #[DataProvider('dotCountDataProvider')]
    public function dotCountFunction(array $test, string $key, int $expected, string $delimiter = Dot::DEFAULT_DELIMITER, int $return = Dot::ZERO_ON_NON_ARRAY) {
        $actual = dotCount($test, $key, $delimiter, $return);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array<mixed>
     */
    public static function dotCountDataProvider(): array {
        return [
            'empty array' => [[], 'a', -1, Dot::DEFAULT_DELIMITER, Dot::NEGATIVE_ON_NON_ARRAY],
            'string value test' => [['a' => ''], 'a', -1, Dot::DEFAULT_DELIMITER, Dot::NEGATIVE_ON_NON_ARRAY],
            'int value test' => [['a' => 123], 'a', -1, Dot::DEFAULT_DELIMITER, Dot::NEGATIVE_ON_NON_ARRAY],
            'float value test' => [['a' => 12.3], 'a', -1, Dot::DEFAULT_DELIMITER, Dot::NEGATIVE_ON_NON_ARRAY],
            'bool value test' => [['a' => false], 'a', -1, Dot::DEFAULT_DELIMITER, Dot::NEGATIVE_ON_NON_ARRAY],
            'empty array zero return' => [[], 'a', 0],
            'string value test zero return' => [['a' => ''], 'a', 0],
            'int value test zero return' => [['a' => 123], 'a', 0],
            'float value test, zero return' => [['a' => 12.3], 'a', 0],
            'bool value test zero return' => [['a' => false], 'a', 0],
            'empty array value test' => [['a' => []], 'a', 0],
            'single value test' => [['a' => ['b']], 'a', 1],
            'multi value test' => [['a' => ['b', 'c', 'd']], 'a', 3],
            'delimiter test' => [['a' => ['b' => ['c', 'd', 'e', 'f']]], 'a~b', 4, '~'],
        ];
    }
}
