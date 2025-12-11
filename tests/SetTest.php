<?php

namespace Tests\AxeTools\Utilities\Dot;

use AxeTools\Utilities\Dot\Dot;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class SetTest extends DotBase {
    /**
     * @param array<mixed>     $test
     * @param non-empty-string $key
     * @param array<mixed>     $expected
     *
     * @return void
     */
    #[Test]
    #[DataProvider('setDataProvider')]
    public function set(array $test, string $key, mixed $value, array $expected) {
        Dot::set($test, $key, $value);
        $this->assertEquals($expected, $test);
    }

    /**
     * @param array<mixed>     $test
     * @param non-empty-string $key
     * @param array<mixed>     $expected
     *
     * @return void
     */
    #[Test]
    #[DataProvider('setDataProvider')]
    public function setCustomDelimiter(array $test, string $key, mixed $value, array $expected) {
        $custom_delimiter = '~';
        $key = str_replace('.', $custom_delimiter, $key);

        Dot::set($test, $key, $value, $custom_delimiter);
        $this->assertEquals($expected, $test);
    }

    /**
     * @param non-empty-string $deliminator
     *
     * @return void
     */
    #[Test]
    #[DataProvider('invalidDelimiterDataProvider')]
    public function setEmptyStringFailure(string $deliminator) {
        $this->expectException(\InvalidArgumentException::class);
        $test = [];
        Dot::set($test, 'test.test', 'test', $deliminator);
    }

    /**
     * @param array<mixed>     $test
     * @param non-empty-string $key
     * @param array<mixed>     $expected
     *
     * @return void
     */
    #[Test]
    #[DataProvider('setDataProvider')]
    public function setFunction(array $test, string $key, mixed $value, array $expected) {
        dotSet($test, $key, $value);
        $this->assertEquals($expected, $test);
    }

    /**
     * @param array<mixed>     $test
     * @param non-empty-string $key
     * @param array<mixed>     $expected
     *
     * @return void
     */
    #[Test]
    #[DataProvider('setDataProvider')]
    public function setFunctionCustomDelimiter(array $test, string $key, mixed $value, array $expected) {
        $custom_delimiter = '~';
        $key = str_replace('.', $custom_delimiter, $key);

        dotSet($test, $key, $value, $custom_delimiter);
        $this->assertEquals($expected, $test);
    }

    /**
     * @param non-empty-string $deliminator
     *
     * @return void
     */
    #[Test]
    #[DataProvider('invalidDelimiterDataProvider')]
    public function setFunctionEmptyStringFailure(string $deliminator) {
        $this->expectException(\InvalidArgumentException::class);
        $test = [];
        dotSet($test, 'test.test', 'test', $deliminator);
    }

    /**
     * @return array<mixed>
     */
    public static function setDataProvider(): array {
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
