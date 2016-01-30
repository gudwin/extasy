<?php


namespace Extasy\tests\Bootstrap;

use Extasy\EnvironmentDetector;

class EnvironmentDetectorTest {
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testUnableToDetect() {
        $locator = new EnvironmentDetector([]);
        $locator->detect(['a' => 1 ]);
    }
    public function testDetect(  ) {
        $map = [
            'local' => [
                ['a' => 1],
                ['b' => 1, 'c' => 1]
            ],
            'tests' => [
                ['d' => 0, 'a' => 1]
            ]
        ];
        $values = ['local','local',''];
        $results = [];
        $locator = new EnvironmentDetector( $map );

        foreach ( $values as $key=>$serverInfo ) {
            $this->AssertEquals( $results[ $key ], $locator->detect( $serverInfo ) );
        }

    }
} 