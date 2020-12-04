<?php


namespace Davajlama\AntLog\Test\Sql;


use Davajlama\AntLog\Sql\Formatter;

class FormatterTest extends \BaseTestCase
{

    public function testClean()
    {
        $formatter = new Formatter();

        $query  = "SELECT * FROM `users` WHERE name = 'david'";
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->assertSame($expect, $formatter->clean($query));

        $query  = "SELECT * FROM `users` WHERE name = ''";
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->assertSame($expect, $formatter->clean($query));

        $query  = 'SELECT * FROM `users` WHERE name = "david"';
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->assertSame($expect, $formatter->clean($query));

        $query  = 'SELECT * FROM `users` WHERE name = ""';
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->assertSame($expect, $formatter->clean($query));

        $query  = 'SELECT * FROM `users` WHERE name = "Teste escaped \" double quotes"';
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->assertSame($expect, $formatter->clean($query));

        $query  = "SELECT * FROM `users` WHERE name = 'Teste escaped \' double quotes'";
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->assertSame($expect, $formatter->clean($query));

        $query  = "SELECT * FROM `users` WHERE name = 123";
        $expect = "SELECT * FROM `users` WHERE name = @value";
        $this->assertSame($expect, $formatter->clean($query));

        $query  = "SELECT * FROM `users` WHERE name2 = 123";
        $expect = "SELECT * FROM `users` WHERE name2 = @value";
        $this->assertSame($expect, $formatter->clean($query));

        $query  = "SELECT * FROM `users` WHERE name2=123";
        $expect = "SELECT * FROM `users` WHERE name2=@value";
        $this->assertSame($expect, $formatter->clean($query));
    }

    public function testFormat()
    {
        $formatter = new Formatter();

        $query = '  SELECT  *  FROM `users`  WHERE id = 1 ';
        $expect = 'SELECT * FROM `users` WHERE id = 1';
        $this->assertSame($expect, $formatter->format($query));

        $query  = 'SELECT * FROM `users` u 
                        LEFT JOIN  `profiles` p ON p.user_id = u.id
                    WHERE id = 1
                    ';

        $expect = 'SELECT * FROM `users` u LEFT JOIN `profiles` p ON p.user_id = u.id WHERE id = 1';
        $this->assertSame($expect, $formatter->format($query));
    }

}