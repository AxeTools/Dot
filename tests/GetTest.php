<?php

namespace Tests\AxeTools\Utilities\Dot;

use AxeTools\Utilities\Dot\Dot;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class GetTest extends DotBase {
    /**
     * @param array<mixed>     $test
     * @param non-empty-string $key
     * @param array|mixed|null $expected
     *
     * @return void
     */
    #[Test]
    #[DataProvider('getDataProvider')]
    public function get(array $test, string $key, mixed $expected, mixed $default = null) {
        $actual = Dot::get($test, $key, $default, Dot::DEFAULT_DELIMITER);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param array<mixed>     $test
     * @param non-empty-string $key
     * @param array|mixed|null $expected
     *
     * @return void
     */
    #[Test]
    #[DataProvider('getDataProvider')]
    public function getCustomDelimiter(array $test, string $key, mixed $expected, mixed $default = null) {
        $custom_delimiter = '~';
        $key = str_replace('.', $custom_delimiter, $key);

        $actual = Dot::get($test, $key, $default, $custom_delimiter);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param array<mixed>     $test
     * @param non-empty-string $key
     * @param array|mixed|null $expected
     *
     * @return void
     */
    #[Test]
    #[DataProvider('getDataProvider')]
    public function getFunction(array $test, string $key, mixed $expected, mixed $default = null) {
        $actual = dotGet($test, $key, $default, Dot::DEFAULT_DELIMITER);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param non-empty-string $deliminator
     *
     * @return void
     */
    #[Test]
    #[DataProvider('invalidDelimiterDataProvider')]
    public function getEmptyStringFailure(string $deliminator) {
        $this->expectException(\InvalidArgumentException::class);
        Dot::get([], 'test.test', null, $deliminator);
    }

    /**
     * @param array<mixed>     $test
     * @param non-empty-string $key
     * @param array|mixed|null $expected
     *
     * @return void
     */
    #[Test]
    #[DataProvider('getDataProvider')]
    public function getFunctionCustomDelimiter(array $test, string $key, mixed $expected, mixed $default = null) {
        $custom_delimiter = '~';
        $key = str_replace('.', $custom_delimiter, $key);

        $actual = dotGet($test, $key, $default, $custom_delimiter);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @param non-empty-string $deliminator
     *
     * @return void
     */
    #[Test]
    #[DataProvider('invalidDelimiterDataProvider')]
    public function getFunctionEmptyStringFailure(string $deliminator) {
        $this->expectException(\InvalidArgumentException::class);
        dotGet([], 'test.test', null, $deliminator);
    }

    /**
     * @return array<mixed>
     */
    public static function getDataProvider(): array {
        return [
            'first level test' => [self::$base_search_array, 'test1', ['test1' => 'test1.test1']],

            'second level test' => [self::$base_search_array, 'test1.test1', 'test1.test1'],
            'second level test not' => [self::$base_search_array, 'test2.test2', "\0"],

            'results type check, int' => [self::$base_search_array, 'test3.int', 1],
            'results type check, float' => [self::$base_search_array, 'test3.float', 1.1],
            'results type check, bool true' => [self::$base_search_array, 'test3.true', true],
            'results type check, bool false' => [self::$base_search_array, 'test3.false', false],
            'results type check, string' => [self::$base_search_array, 'test3.string', 'string'],

            'key not found test, default default' => [self::$base_search_array, 'notthere', null],

            'key not found test, int default' => [self::$base_search_array, 'notthere', 0, 0],
            'key not found test, false default' => [self::$base_search_array, 'notthere', false, false],
            'key not found test, true default' => [self::$base_search_array, 'notthere', true, true],
            'key not found test, float default' => [self::$base_search_array, 'notthere', 1.1, 1.1],
            'key not found test, string default' => [self::$base_search_array, 'notthere', 'nonya', 'nonya'],

            'mixed key types' => [self::$base_search_array, 'test4.0.test4.0', 'test4'],
            'mixed key types multi int' => [self::$base_search_array, 'test5.0.test5.0.0', 'test5'],
        ];
    }
}
