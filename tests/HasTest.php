<?php

namespace Tests\AxeTools\Utilities\Dot;

use AxeTools\Utilities\Dot\Dot;

class HasTest extends DotBase {
    /**
     * @test
     *
     * @dataProvider hasDataProvider
     *
     * @param array<mixed>     $test
     * @param non-empty-string $key
     * @param non-empty-string $delimiter
     *
     * @return void
     */
    public function has(array $test, string $key, bool $expected, string $delimiter = Dot::DEFAULT_DELIMITER) {
        $actual = Dot::has($test, $key, $delimiter);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     *
     * @param non-empty-string $deliminator
     *
     * @dataProvider invalidDelimiterDataProvider
     *
     * @return void
     */
    public function hasEmptyStringFailure(string $deliminator) {
        $this->expectException(\InvalidArgumentException::class);
        Dot::has([], 'test.test', $deliminator);
    }

    /**
     * @test
     *
     * @dataProvider hasDataProvider
     *
     * @param array<mixed>     $test
     * @param non-empty-string $key
     * @param non-empty-string $delimiter
     *
     * @return void
     */
    public function hasFunction(array $test, string $key, bool $expected, string $delimiter = Dot::DEFAULT_DELIMITER) {
        $actual = dotHas($test, $key, $delimiter);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     *
     * @param non-empty-string $deliminator
     *
     * @dataProvider invalidDelimiterDataProvider
     *
     * @return void
     */
    public function hasFunctionEmptyStringFailure(string $deliminator) {
        $this->expectException(\InvalidArgumentException::class);
        dotHas([], 'test.test', $deliminator);
    }

    /**
     * @return array<mixed>
     */
    public static function hasDataProvider(): array {
        return [
            'single level present' => [self::$base_search_array, 'test1', true],
            'single level not present' => [self::$base_search_array, 'notthere', false],

            'multiple level present' => [self::$base_search_array, 'test1.test1', true],
            'multiple level present custom delim' => [self::$base_search_array, 'test1~test1', true, '~'],
            'multiple level not present' => [self::$base_search_array, 'test1.notthere', false],
            'multiple level not present custom delim' => [self::$base_search_array, 'test1~notthere', false, '~'],
            'multiple level many not present custom delim' => [self::$base_search_array, 'test1~test1~blah1~blah2~blah3~blah4~blah5', false, '~'],
        ];
    }
}
