<?php

namespace Tests\AxeTools\Utilities\Dot;

use AxeTools\Utilities\Dot\Dot;
use InvalidArgumentException;

class SetTest extends DotBase {
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
        Dot::set($test, $key, $value);
        $this->assertEquals($expected, $test);
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

        Dot::set($test, $key, $value, $custom_delimiter);
        $this->assertEquals($expected, $test);
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
        Dot::set($test, 'test.test', 'test', $deliminator);
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
    public function setFunction(array $test, $key, $value, $expected) {
        dotSet($test, $key, $value);
        $this->assertEquals($expected, $test);
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
    public function setFunctionCustomDelimiter(array $test, $key, $value, array $expected) {
        $custom_delimiter = '~';
        $key = str_replace('.', $custom_delimiter, $key);

        dotSet($test, $key, $value, $custom_delimiter);
        $this->assertEquals($expected, $test);
    }

    /**
     * @test
     *
     * @param non-empty-string $deliminator
     *
     * @return void
     * @dataProvider invalidDelimiterDataProvider
     */
    public function setFunctionEmptyStringFailure($deliminator) {
        $this->expectException(InvalidArgumentException::class);
        $test = [];
        dotSet($test, 'test.test', 'test', $deliminator);
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
}
