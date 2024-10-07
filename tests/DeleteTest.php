<?php

namespace Tests\AxeTools\Utilities\Dot;

use AxeTools\Utilities\Dot\Dot;

class DeleteTest extends DotBase {
    /**
     * @test
     * @dataProvider deleteDataProvider
     *
     * @param        $key
     * @param        $expected
     * @param string $delimiter
     *
     * @return void
     */
    public function dotDelete(array $array, $key, $expected, $delimiter = Dot::DEFAULT_DELIMITER) {
        Dot::delete($array, $key, $delimiter);
        $this->assertEquals($expected, $array);
    }

    /**
     * @test
     * @dataProvider deleteDataProvider
     *
     * @param        $key
     * @param        $expected
     * @param string $delimiter
     *
     * @return void
     */
    public function dotDeleteFunction(array $array, $key, $expected, $delimiter = Dot::DEFAULT_DELIMITER) {
        dotDelete($array, $key, $delimiter);
        $this->assertEquals($expected, $array);
    }

    /**
     * @return mixed[]
     */
    public static function deleteDataProvider() {
        return [
            'delete a value' => [['a' => ['b' => ['c' => 'd']]], 'a.b.c', ['a' => ['b' => []]]],
            'delete a value in set' => [['a' => ['b' => ['c' => 'd', 'e' => 'f']]], 'a.b.e', ['a' => ['b' => ['c' => 'd']]]],
            'delete a value in mixed set' => [['a' => ['b' => ['c' => 'd', 'e' => 'f', 'g' => ['h' => 'i']]]], 'a.b.e', ['a' => ['b' => ['c' => 'd', 'g' => ['h' => 'i']]]]],
            'delete a array' => [['a' => ['b' => ['c' => 'd']]], 'a.b', ['a' => []]],
            'no key found' => [['a' => ['b' => ['c' => 'd']]], 'a.b.c.e', ['a' => ['b' => ['c' => 'd']]]],
        ];
    }
}
