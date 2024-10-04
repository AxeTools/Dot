<?php

namespace Tests\AxeTools\Utilities\Dot;

use AxeTools\Utilities\Dot\Dot;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DotFunctionsTest extends TestCase {
    /**
     * @test
     * @dataProvider setDataProvider
     *
     * @param mixed[]          $test
     * @param non-empty-string $key
     * @param mixed            $value
     * @param mixed[]          $expected
     *
     * @return void
     */
    public function set(array $test, $key, $value, $expected) {
        dotSet($test, $key, $value);
        $this->assertEquals($expected, $test);
    }

    /**
     * @test
     * @dataProvider getDataProvider
     *
     * @param mixed[]          $test
     * @param non-empty-string $key
     * @param array|mixed|null $expected
     * @param mixed|null       $default
     *
     * @return void
     */
    public function get(array $test, $key, $expected, $default = null) {
        $actual = dotGet($test, $key, $default, Dot::DEFAULT_DELIMITER);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider hasDataProvider
     *
     * @param mixed[]          $test
     * @param non-empty-string $key
     * @param bool             $expected
     * @param non-empty-string $delimiter
     *
     * @return void
     */
    public function has(array $test, $key, $expected, $delimiter = Dot::DEFAULT_DELIMITER) {
        $actual = dotHas($test, $key, $delimiter);
        $this->assertEquals($expected, $actual);
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
    public function increment(array $array, $key, $incrementor, $default, $expected, $endArray) {
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
    public function incrementOverLoop($array, $key, $incrementor, $default, $loopCount, $expected, $endArray) {
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

    /**
     * @test
     * @dataProvider dotCountDataProvider
     *
     * @param mixed[]          $test
     * @param non-empty-string $key
     * @param int              $expected
     * @param non-empty-string $delimiter
     *
     * @return void
     */
    public function dotCount(array $test, $key, $expected, $delimiter = Dot::DEFAULT_DELIMITER, $return = Dot::ZERO_ON_NON_ARRAY) {
        $actual = dotCount($test, $key, $delimiter, $return);
        $this->assertEquals($expected, $actual);
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
    public function flatten(array $test, array $expected) {
        $actual = dotFlatten($test);
        $this->assertEquals($expected, $actual);

        // There should never be an array value in a flattened response
        foreach ($actual as $value) {
            $this->assertFalse(is_array($value));
        }
    }

    /**
     * @test
     * @dataProvider setDataProvider
     *
     * @param mixed[]          $test
     * @param non-empty-string $key
     * @param mixed            $value
     * @param mixed[]          $expected
     *
     * @return void
     */
    public function setCustomDelimiter(array $test, $key, $value, array $expected) {
        $custom_delimiter = '~';
        $key = str_replace('.', $custom_delimiter, $key);

        dotSet($test, $key, $value, $custom_delimiter);
        $this->assertEquals($expected, $test);
    }

    /**
     * @test
     * @dataProvider getDataProvider
     *
     * @param mixed[]          $test
     * @param non-empty-string $key
     * @param array|mixed|null $expected
     * @param mixed|null       $default
     *
     * @return void
     */
    public function getCustomDelimiter(array $test, $key, $expected, $default = null) {
        $custom_delimiter = '~';
        $key = str_replace('.', $custom_delimiter, $key);

        $actual = dotGet($test, $key, $default, $custom_delimiter);
        $this->assertEquals($expected, $actual);
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
     *
     * @return void
     * @dataProvider invalidDelimiterDataProvider
     */
    public function getEmptyStringFailure($deliminator) {
        $this->expectException(InvalidArgumentException::class);
        dotGet([], 'test.test', null, $deliminator);
    }

    /**
     * @test
     *
     * @param non-empty-string $deliminator
     *
     * @return void
     * @dataProvider invalidDelimiterDataProvider
     */
    public function setEmptyStringFailure($deliminator) {
        $this->expectException(InvalidArgumentException::class);
        $test = [];
        dotSet($test, 'test.test', 'test', $deliminator);
    }

    /**
     * @test
     *
     * @param non-empty-string $deliminator
     * @dataProvider invalidDelimiterDataProvider
     *
     * @return void
     */
    public function hasEmptyStringFailure($deliminator) {
        $this->expectException(InvalidArgumentException::class);
        dotHas([], 'test.test', $deliminator);
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
        dotFlatten([], $deliminator);
    }

    /**
     * @test
     * @dataProvider appendDataProvider
     *
     * @param $key
     * @param $value
     * @param $expected
     *
     * @return void
     */
    public function append(array $array, $key, $value, $expected) {
        dotAppend($array, $key, $value);
        $this->assertEquals($expected, $array);
    }

    /**
     * @test
     * @dataProvider deleteDataProvider
     *
     * @param $key
     * @param $expected
     * @param $delimiter
     *
     * @return void
     */
    public function dotDelete(array $array, $key, $expected, $delimiter = Dot::DEFAULT_DELIMITER) {
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

    /**
     * @return mixed[]
     */
    public static function appendDataProvider() {
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

    /**
     * @return mixed[]
     */
    public static function setDataProvider() {
        return [
            'single dimension set' => [['test' => 'test'], 'test', 'new', ['test' => 'new']],
            'single dimension create' => [[], 'test', 'new', ['test' => 'new']],

            'multi dimension set' => [['test' => ['test' => 'test']], 'test.test', 'new', ['test' => ['test' => 'new']]],
            'multi dimension set non key' => [['test' => ['test']], 'test.0', 'new', ['test' => ['new']]],

            'multi dimension create' => [[], 'test.test', 'new', ['test' => ['test' => 'new']]],
            'multi dimension create non key' => [[], 'test.0', 'new', ['test' => ['new']]],

            'multi dimension multi non key set' => [
                ['test' => [['test']]],
                'test.0.0',
                'new',
                ['test' => [['new']]],
            ],
            'multi dimension multi non key create' => [
                [],
                'test.0.0',
                'new',
                ['test' => [['new']]],
            ],
            'multi dimension multi non key create part' => [
                ['test' => []],
                'test.0.0',
                'new',
                ['test' => [['new']]],
            ],

            'multi dimension multi mixed set' => [
                ['test' => [['test' => 'test']]],
                'test.0.test',
                'new',
                ['test' => [['test' => 'new']]],
            ],
            'multi dimension multi mixed create' => [
                [],
                'test.0.test',
                'new',
                ['test' => [['test' => 'new']]],
            ],
            'multi dimension multi mixed create part' => [
                ['test' => ['test']],
                'test.1.test',
                'new',
                ['test' => ['test', ['test' => 'new']]],
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public static function getDataProvider() {
        $base_search_array = [
            'test1' => [
                'test1' => 'test1.test1',
            ],
            'test2' => [
                'test2' => "\0",
            ],
            'test3' => [
                'int' => 1,
                'float' => 1.1,
                'string' => 'string',
                'true' => true,
                'false' => false,
            ],
            'test4' => [
                [
                    'test4' => ['test4'],
                ],
            ],
            'test5' => [
                [
                    'test5' => [
                        ['test5'],
                    ],
                ],
            ],
        ];

        return [
            'first level test' => [$base_search_array, 'test1', ['test1' => 'test1.test1']],

            'second level test' => [$base_search_array, 'test1.test1', 'test1.test1'],
            'second level test not' => [$base_search_array, 'test2.test2', "\0"],

            'results type check, int' => [$base_search_array, 'test3.int', 1],
            'results type check, float' => [$base_search_array, 'test3.float', 1.1],
            'results type check, bool true' => [$base_search_array, 'test3.true', true],
            'results type check, bool false' => [$base_search_array, 'test3.false', false],
            'results type check, string' => [$base_search_array, 'test3.string', 'string'],

            'key not found test, default default' => [$base_search_array, 'notthere', null],

            'key not found test, int default' => [$base_search_array, 'notthere', 0, 0],
            'key not found test, false default' => [$base_search_array, 'notthere', false, false],
            'key not found test, true default' => [$base_search_array, 'notthere', true, true],
            'key not found test, float default' => [$base_search_array, 'notthere', 1.1, 1.1],
            'key not found test, string default' => [$base_search_array, 'notthere', 'nonya', 'nonya'],

            'mixed key types' => [$base_search_array, 'test4.0.test4.0', 'test4'],
            'mixed key types multi int' => [$base_search_array, 'test5.0.test5.0.0', 'test5'],
        ];
    }

    /**
     * @return mixed[]
     */
    public static function hasDataProvider() {
        $base_search_array = [
            'test1' => [
                'test1' => 'test1.test1',
            ],
            'test2' => [
                'test2' => "\0",
            ],
            'test3' => [
                'int' => 1,
                'float' => 1.1,
                'string' => 'string',
                'true' => true,
                'false' => false,
            ],
            'test4' => [
                [
                    'test4' => ['test4'],
                ],
            ],
            'test5' => [
                [
                    'test5' => [
                        ['test5'],
                    ],
                ],
            ],
        ];

        return [
            'single level present' => [$base_search_array, 'test1', true],
            'single level not present' => [$base_search_array, 'notthere', false],

            'multiple level present' => [$base_search_array, 'test1.test1', true],
            'multiple level present custom delim' => [$base_search_array, 'test1~test1', true, '~'],
            'multiple level not present' => [$base_search_array, 'test1.notthere', false],
            'multiple level not present custom delim' => [$base_search_array, 'test1~notthere', false, '~'],
            'multiple level many not present custom delim' => [$base_search_array, 'test1~test1~blah1~blah2~blah3~blah4~blah5', false, '~'],
        ];
    }

    /**
     * @return mixed[]
     */
    public static function dotCountDataProvider() {
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

    /**
     * @return array<string, mixed>
     */
    public static function invalidDelimiterDataProvider() {
        return [
            'Empty String Deliminator' => [''],
            'Null Deliminator' => [null],
            'Array Deliminator' => [[]],
        ];
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
}
