<?php

namespace Tests\AxeTools\Utilities\Dot;

use AxeTools\Utilities\Dot\Dot;
use InvalidArgumentException;

class FlattenTest extends DotBase {
    /**
     * @test
     * @dataProvider flattenDataProvider
     *
     * @param mixed[]                        $test
     * @param array<non-empty-string, mixed> $expected
     *
     * @return void
     */
    public function flatten(array $test, array $expected) {
        $actual = Dot::flatten($test);
        $this->assertEquals($expected, $actual);

        // There should never be an array value in a flattened response
        foreach ($actual as $value) {
            $this->assertFalse(is_array($value));
        }
    }

    /**
     * @test
     * @dataProvider flattenWithCustomDelimitersDataProvider
     *
     * @param non-empty-string $delimiter
     * @param mixed[]          $test
     * @param mixed[]          $expected
     *
     * @return void
     */
    public function flattenWithCustomDelimiters($delimiter, array $test, array $expected) {
        $actual = Dot::flatten($test, $delimiter);
        $this->assertEquals($expected, $actual);
        $reset = [];
        foreach ($actual as $key => $value) {
            Dot::set($reset, $key, $value, $delimiter);
        }
        $this->assertEquals($test, $reset);
    }

    /**
     * @test
     *
     * @param non-empty-string $deliminator
     * @dataProvider invalidDelimiterDataProvider
     *
     * @return void
     */
    public function flattenEmptyStringFailure($deliminator) {
        $this->expectException(InvalidArgumentException::class);
        Dot::flatten([], $deliminator);
    }

    /**
     * @test
     * @dataProvider flattenDataProvider
     *
     * @param mixed[]                        $test
     * @param array<non-empty-string, mixed> $expected
     *
     * @return void
     */
    public function flattenFunction(array $test, array $expected) {
        $actual = dotFlatten($test);
        $this->assertEquals($expected, $actual);

        // There should never be an array value in a flattened response
        foreach ($actual as $value) {
            $this->assertFalse(is_array($value));
        }
    }

    /**
     * @test
     * @dataProvider flattenWithCustomDelimitersDataProvider
     *
     * @param non-empty-string $delimiter
     * @param mixed[]          $test
     * @param mixed[]          $expected
     *
     * @return void
     */
    public function flattenFunctionWithCustomDelimiters($delimiter, array $test, array $expected) {
        $actual = dotFlatten($test, $delimiter);
        $this->assertEquals($expected, $actual);
        $reset = [];
        foreach ($actual as $key => $value) {
            dotSet($reset, $key, $value, $delimiter);
        }
        $this->assertEquals($test, $reset);
    }

    /**
     * @test
     *
     * @param non-empty-string $deliminator
     * @dataProvider invalidDelimiterDataProvider
     *
     * @return void
     */
    public function flattenFunctionEmptyStringFailure($deliminator) {
        $this->expectException(InvalidArgumentException::class);
        dotFlatten([], $deliminator);
    }

    /**
     * @return mixed[]
     */
    public static function flattenDataProvider() {
        return [
            'single item pass through' => [['test' => 'test'], ['test' => 'test']],
            'single nested item' => [['test' => ['test' => 'test']], ['test.test' => 'test']],
            'multi items pass through' => [
                ['test1' => 'test1', 'test2' => 'test2'],
                ['test1' => 'test1', 'test2' => 'test2'],
            ],
            'multi nested items' => [
                ['test1' => ['test1' => 'test1'], 'test2' => 'test2'],
                ['test1.test1' => 'test1', 'test2' => 'test2'],
            ],
            'multi nested items non string keys' => [
                ['test1' => ['test1' => 'test1'], 'test2' => ['test2']],
                ['test1.test1' => 'test1', 'test2.0' => 'test2'],
            ],
            'multi nested items multiple non string keys' => [
                ['test1' => ['test1' => 'test1'], 'test2' => ['test2', 'test2a']],
                ['test1.test1' => 'test1', 'test2.0' => 'test2', 'test2.1' => 'test2a'],
            ],
            'deep nested items multiple non string keys' => [
                ['test1' => ['test1' => ['test1' => [['test1']]]], 'test2' => ['test2', 'test2a']],
                ['test1.test1.test1.0.0' => 'test1', 'test2.0' => 'test2', 'test2.1' => 'test2a'],
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public static function flattenWithCustomDelimitersDataProvider() {
        return [
            'single nested item' => ['_', ['test' => ['test' => 'test']], ['test_test' => 'test']],
            'multi nested items' => [
                '--',
                ['test1' => ['test1' => 'test1'], 'test2' => 'test2'],
                ['test1--test1' => 'test1', 'test2' => 'test2'],
            ],
            'multi nested items multiple non string keys' => [
                '*',
                ['test1' => ['test1' => 'test1'], 'test2' => ['test2', 'test2a']],
                ['test1*test1' => 'test1', 'test2*0' => 'test2', 'test2*1' => 'test2a'],
            ],
            'deep nested items multiple non string keys' => [
                '~',
                ['test1' => ['test1' => ['test1' => [['test1']]]], 'test2' => ['test2', 'test2a']],
                ['test1~test1~test1~0~0' => 'test1', 'test2~0' => 'test2', 'test2~1' => 'test2a'],
            ],
        ];
    }
}
