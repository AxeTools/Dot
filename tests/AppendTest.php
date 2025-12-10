<?php

namespace Tests\AxeTools\Utilities\Dot;

use AxeTools\Utilities\Dot\Dot;

class AppendTest extends DotBase {
    /**
     * @test
     *
     * @dataProvider appendDataProvider
     *
     * @param array<mixed>     $array
     * @param non-empty-string $key
     * @param array<mixed>     $expected
     *
     * @return void
     */
    public function append(array $array, string $key, mixed $value, array $expected) {
        Dot::append($array, $key, $value);
        $this->assertEquals($expected, $array);
    }

    /**
     * @test
     *
     * @dataProvider appendDataProvider
     *
     * @param array<mixed>     $array
     * @param non-empty-string $key
     * @param array<mixed>     $expected
     *
     * @return void
     */
    public function appendFunction(array $array, string $key, mixed $value, array $expected) {
        dotAppend($array, $key, $value);
        $this->assertEquals($expected, $array);
    }

    /**
     * @return array<mixed>
     */
    public static function appendDataProvider(): array {
        return [
            'append to array' => [['test' => ['test1']], 'test', 'test2', ['test' => ['test1', 'test2']]],
            'append to string value' => [['test' => 'test1'], 'test', 'test2', ['test' => ['test1', 'test2']]],
            'append to int value' => [['test' => 'test1'], 'test', 1, ['test' => ['test1', 1]]],
            'append to float value' => [['test' => 'test1'], 'test', 1.1, ['test' => ['test1', 1.1]]],
            'append to bool value' => [['test' => 'test1'], 'test', false, ['test' => ['test1', false]]],
            'append to null value' => [['test' => 'test1'], 'test', null, ['test' => ['test1', null]]],
            'append to arrays' => [['test' => ['test1' => ['test3']]], 'test', 'test2', ['test' => ['test1' => ['test3'], 'test2']]],
            'append to nothing' => [[], 'test', 'test1', ['test' => ['test1']]],
            'append to deep array' => [['test' => ['test1' => ['test2' => ['test3']]]], 'test.test1.test2', 'test4', ['test' => ['test1' => ['test2' => ['test3', 'test4']]]]],
            'append to key value array' => [['test' => ['test1' => ['test3' => 'test4']]], 'test.test1', 'test5', ['test' => ['test1' => ['test3' => 'test4', 'test5']]]],
        ];
    }
}
