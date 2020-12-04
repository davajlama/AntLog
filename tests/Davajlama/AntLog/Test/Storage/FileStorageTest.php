<?php


namespace Davajlama\AntLog\Test\Storage;


use Davajlama\AntLog\Storage\FileStorage;

class FileStorageTest extends \BaseTestCase
{

    public function testCreateFilename()
    {
        $storage = new FileStorage(__DIR__ . '/../../Fixtures/temp');

        $type   = 'sql';
        $runner = 'ab1231';
        $date   = date('Ymd');

        $expect = 'sql.ab1231.' . $date . '.antlog';
        $this->assertSame($expect, $storage->createFileName($type, $runner));
    }

}