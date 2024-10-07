<?php

namespace Tests\AxeTools\Utilities\Dot;

use PHPUnit\Framework\TestCase;

abstract class DotBase extends TestCase {
    protected static $base_search_array = [
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
}
