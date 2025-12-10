<?php

namespace Tests\AxeTools\Utilities\Dot;

use AxeTools\Utilities\Dot\Dot;

class IncrementTest extends DotBase {
    /**
     * @test
     *
     * @dataProvider incrementorDataProvider
     *
     * @param array<mixed>     $array
     * @param non-empty-string $key
     * @param float|int        $incrementor
     * @param float|int        $default
     * @param float|int        $expected
     * @param array<mixed>     $endArray
     *
     * @return void
     */
    public function increment(array $array, $key, $incrementor, $default, $expected, $endArray) {
        $actual = Dot::increment($array, $key, $incrementor, $default);

        $this->assertEqualsWithDelta($expected, $actual, 0.0001, 'expected return');
        $this->assertEqualsWithDelta($endArray, $array, 0.0001, 'expected end array');
    }

    /**
     * @test
     *
     * @dataProvider incrementOverLoopDataProvider
     *
     * @param array<mixed>     $array
     * @param non-empty-string $key
     * @param array<mixed>     $endArray
     *
     * @return void
     */
    public function incrementOverLoop(array $array, string $key, float|int $incrementor, float|int $default, int $loopCount, float|int $expected, array $endArray) {
        $result = -1000000;

        foreach (range(1, $loopCount) as $_) {
            $result = Dot::increment($array, $key, $incrementor, $default);
        }

        $this->assertEqualsWithDelta($expected, $result, 0.0001, 'expected return');
        $this->assertEqualsWithDelta($endArray, $array, 0.0001, 'expected end array');
    }

    /**
     * @test
     *
     * @dataProvider incrementorDataProvider
     *
     * @param array<mixed>     $array
     * @param non-empty-string $key
     * @param array<mixed>     $endArray
     *
     * @return void
     */
    public function incrementFunction(array $array, string $key, float|int $incrementor, float|int $default, float|int $expected, array $endArray) {
        $actual = dotIncrement($array, $key, $incrementor, $default);

        $this->assertEqualsWithDelta($expected, $actual, 0.0001, 'expected return');
        $this->assertEqualsWithDelta($endArray, $array, 0.0001, 'expected end array');
    }

    /**
     * @test
     *
     * @dataProvider incrementOverLoopDataProvider
     *
     * @param array<mixed>     $array
     * @param non-empty-string $key
     * @param array<mixed>     $endArray
     *
     * @return void
     */
    public function incrementFunctionOverLoop(array $array, string $key, float|int $incrementor, float|int $default, int $loopCount, float|int $expected, array $endArray) {
        $result = -1000000;
        foreach (range(1, $loopCount) as $_) {
            $result = dotIncrement($array, $key, $incrementor, $default);
        }

        $this->assertEqualsWithDelta($expected, $result, 0.0001, 'expected return');
        $this->assertEqualsWithDelta($endArray, $array, 0.0001, 'expected end array');
    }

    /**
     * @return array<mixed>
     */
    public static function incrementorDataProvider(): array {
        return [
            'No initial value positive increment, 0 default' => [[], 'test', 1, 0, 1, ['test' => 1]],
            'No initial value negative increment, 0 default' => [[], 'test', -1, 0, -1, ['test' => -1]],
            'No initial value positive increment, non 0 default' => [[], 'test', 1, 1.1, 2.1, ['test' => 2.1]],
            'No initial value negative increment, non 0 default' => [[], 'test', -1, 1.1, 0.1, ['test' => 0.1]],
            'Initial value positive increment, default ignored' => [['test' => 0.2], 'test', 1, 0, 1.2, ['test' => 1.2]],
            'Initial value negative increment, default ignored' => [['test' => 0.2], 'test', -1, 0, -0.8, ['test' => -0.8]],
        ];
    }

    /**
     * @return array<mixed>
     */
    public static function incrementOverLoopDataProvider(): array {
        return [
            'No initial value positive increment, 0 default' => [[], 'test', 1, 0, 50, 50, ['test' => 50]],
            'No initial value negative increment, 0 default' => [[], 'test', -1, 0, 50, -50, ['test' => -50]],
            'No initial value positive increment, non 0 default' => [[], 'test', 1.1, 1, 50, 56, ['test' => 56]],
            'No initial value negative increment, non 0 default' => [[], 'test', -1.1, 1, 50, -54, ['test' => -54]],
            'Initial value positive increment, default ignored' => [['test' => 0.2], 'test', 1, 0, 50, 50.2, ['test' => 50.2]],
            'Initial value negative increment, default ignored' => [['test' => 0.2], 'test', -1, 0, 50, -49.8, ['test' => -49.8]],
        ];
    }
}
