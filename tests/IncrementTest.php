<?php

namespace Tests\AxeTools\Utilities\Dot;

use AxeTools\Utilities\Dot\Dot;
use InvalidArgumentException;

class IncrementTest extends DotBase {
    /**
     * @test
     * @dataProvider incrementorDataProvider
     *
     * @param $key
     * @param $incrementor
     * @param $default
     * @param $expected
     * @param $endArray
     *
     * @return void
     */
    public function increment(array $array, $key, $incrementor, $default, $expected, $endArray) {
        $actual = Dot::increment($array, $key, $incrementor, $default);
        /* transitional workaround for phpunit on php ^7.1 || ^8.0 */
        if (method_exists($this, 'assertEqualsWithDelta')) {
            $this->assertEqualsWithDelta($expected, $actual, 0.0001, 'expected return');
            $this->assertEqualsWithDelta($endArray, $array, 0.0001, 'expected end array');
        } else {
            $this->assertEquals($expected, $actual, 'expected return', 0.000001);
            $this->assertEquals($endArray, $array, 'expected end array', 0.000001);
        }
    }

    /**
     * @test
     * @dataProvider incrementOverLoopDataProvider
     *
     * @param $array
     * @param $key
     * @param $incrementor
     * @param $default
     * @param $loopCount
     * @param $expected
     * @param $endArray
     *
     * @return void
     */
    public function incrementOverLoop($array, $key, $incrementor, $default, $loopCount, $expected, $endArray) {
        foreach (range(1, $loopCount) as $_) {
            $result = Dot::increment($array, $key, $incrementor, $default);
        }
        /* transitional workaround for phpunit on php ^7.1 || ^8.0 */
        if (method_exists($this, 'assertEqualsWithDelta')) {
            $this->assertEqualsWithDelta($expected, $result, 0.0001, 'expected return');
            $this->assertEqualsWithDelta($endArray, $array, 0.0001, 'expected end array');
        } else {
            $this->assertEquals($expected, $result, 'expected return', 0.000001);
            $this->assertEquals($endArray, $array, 'expected end array', 0.000001);
        }
    }

    /**
     * @test
     * @dataProvider incrementInvalidConditionDataProvider
     *
     * @param mixed $incrementor
     * @param mixed $default
     *
     * @return void
     */
    public function incrementInvalidConditions($incrementor, $default) {
        $this->expectException(InvalidArgumentException::class);
        $array = [];
        Dot::increment($array, 'test', $incrementor, $default);
    }

    /**
     * @test
     * @dataProvider incrementorDataProvider
     *
     * @param $key
     * @param $incrementor
     * @param $default
     * @param $expected
     * @param $endArray
     *
     * @return void
     */
    public function incrementFunction(array $array, $key, $incrementor, $default, $expected, $endArray) {
        $actual = dotIncrement($array, $key, $incrementor, $default);
        if (method_exists($this, 'assertEqualsWithDelta')) {
            $this->assertEqualsWithDelta($expected, $actual, 0.0001, 'expected return');
            $this->assertEqualsWithDelta($endArray, $array, 0.0001, 'expected end array');
        } else {
            $this->assertEquals($expected, $actual, 'expected return', 0.000001);
            $this->assertEquals($endArray, $array, 'expected end array', 0.000001);
        }
    }

    /**
     * @test
     * @dataProvider incrementOverLoopDataProvider
     *
     * @param $array
     * @param $key
     * @param $incrementor
     * @param $default
     * @param $loopCount
     * @param $expected
     * @param $endArray
     *
     * @return void
     */
    public function incrementFunctionOverLoop($array, $key, $incrementor, $default, $loopCount, $expected, $endArray) {
        foreach (range(1, $loopCount) as $_) {
            $result = dotIncrement($array, $key, $incrementor, $default);
        }
        if (method_exists($this, 'assertEqualsWithDelta')) {
            $this->assertEqualsWithDelta($expected, $result, 0.0001, 'expected return');
            $this->assertEqualsWithDelta($endArray, $array, 0.0001, 'expected end array');
        } else {
            $this->assertEquals($expected, $result, 'expected return', 0.000001);
            $this->assertEquals($endArray, $array, 'expected end array', 0.000001);
        }
    }

    public static function incrementorDataProvider() {
        return [
            'No initial value positive increment, 0 default' => [[], 'test', 1, 0, 1, ['test' => 1]],
            'No initial value negative increment, 0 default' => [[], 'test', -1, 0, -1, ['test' => -1]],
            'No initial value positive increment, non 0 default' => [[], 'test', 1, 1.1, 2.1, ['test' => 2.1]],
            'No initial value negative increment, non 0 default' => [[], 'test', -1, 1.1, 0.1, ['test' => 0.1]],
            'Initial value positive increment, default ignored' => [['test' => 0.2], 'test', 1, 0, 1.2, ['test' => 1.2]],
            'Initial value negative increment, default ignored' => [['test' => 0.2], 'test', -1, 0, -0.8, ['test' => -0.8]],
        ];
    }

    public static function incrementOverLoopDataProvider() {
        return [
            'No initial value positive increment, 0 default' => [[], 'test', 1, 0, 50, 50, ['test' => 50]],
            'No initial value negative increment, 0 default' => [[], 'test', -1, 0, 50, -50, ['test' => -50]],
            'No initial value positive increment, non 0 default' => [[], 'test', 1.1, 1, 50, 56, ['test' => 56]],
            'No initial value negative increment, non 0 default' => [[], 'test', -1.1, 1, 50, -54, ['test' => -54]],
            'Initial value positive increment, default ignored' => [['test' => 0.2], 'test', 1, 0, 50, 50.2, ['test' => 50.2]],
            'Initial value negative increment, default ignored' => [['test' => 0.2], 'test', -1, 0, 50, -49.8, ['test' => -49.8]],
        ];
    }

    public static function incrementInvalidConditionDataProvider() {
        return [
            'invalid string incrementor, positive default' => ['things', 1],
            'invalid array incrementor, positive default' => [[], 1],
            'invalid null incrementor, positive default' => [null, 1],
            'valid incrementor, invalid string default' => [1, 'things'],
            'valid incrementor, invalid array default' => [1, []],
            'valid incrementor, invalid null default' => [1, null],
            'invalid incrementor, invalid default' => [null, null],
        ];
    }
}
