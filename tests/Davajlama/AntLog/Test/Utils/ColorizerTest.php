<?php


namespace Davajlama\AntLog\Test\Utils;


use Davajlama\AntLog\Utils\Colorizer;

class ColorizerTest extends \BaseTestCase
{

    public function testGreen()
    {
        $colorizer = new Colorizer();

        $input = 'AntLog is cool';
        $expect = "\033[0;32m" . $input . "\033[0m";

        $this->assertSame($expect, $colorizer->green($input));
    }

}