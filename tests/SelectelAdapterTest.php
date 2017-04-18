<?php

use ArgentCrusade\Flysystem\Selectel\SelectelAdapter;
use ArgentCrusade\Selectel\CloudStorage\Collections\Collection;
use League\Flysystem\Config;

class SelectelAdapterTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        Mockery::close();
    }

    public function selectelProvider()
    {
        $collection = new Collection([
            [
                'name' => 'path/to/file',
                'content_type' => 'text/plain',
                'bytes' => 1024,
                'last_modified' => '2000-01-01 00:00:00',
            ],
        ]);

        $files = Mockery::mock('ArgentCrusade\Selectel\CloudStorage\FluentFilesLoader');
        $files->shouldReceive('withPrefix')->andReturn($files);
        $files->shouldReceive('get')->andReturn($collection);

        $mock = Mockery::mock('ArgentCrusade\Selectel\CloudStorage\Container');
        $mock->shouldReceive('type')->andReturn('public');
        $mock->shouldReceive('files')->andReturn($files);

        return [
            [new SelectelAdapter($mock), $mock, $files, $collection],
        ];
    }

    public function metaDataProvider()
    {
        $collection = new Collection([
            [
                'name' => 'path/to/file',
                'content_type' => 'text/plain',
                'bytes' => 1024,
                'last_modified' => '2000-01-01 00:00:00',
            ],
        ]);

        $files = Mockery::mock('ArgentCrusade\Selectel\CloudStorage\FluentFilesLoader');
        $files->shouldReceive('withPrefix')->andReturn($files);
        $files->shouldReceive('get')->andReturn($collection);

        $mock = Mockery::mock('ArgentCrusade\Selectel\CloudStorage\Container');
        $mock->shouldReceive('type')->andReturn('public');
        $mock->shouldReceive('files')->andReturn($files);

        $adapter = new SelectelAdapter($mock);

        return [
            [
                'method' => 'getMetadata',
                'adapter' => $adapter,
            ],
            [
                'method' => 'getMimetype',
                'adapter' => $adapter,
            ],
            [
                'method' => 'getTimestamp',
                'adapter' => $adapter,
            ],
            [
                'method' => 'getSize',
                'adapter' => $adapter,
            ],
        ];
    }

    /**
     * @dataProvider metaDataProvider
     */
    public function testMetaData($method, $adapter)
    {
        $result = $adapter->{$method}('path');

        $this->assertInternalType('array', $result);
    }

    /**
     * @dataProvider selectelProvider
     */
    public function testHas($adapter, $mock, $files)
    {
        $files->shouldReceive('exists')->andReturn(true);

        $this->assertTrue($adapter->has('something'));
    }

    /**
     * @dataProvider selectelProvider
     */
    public function testUrl($adapter, $mock, $files)
    {
        $mock->shouldReceive('url')->with('/file.txt')->andReturn('https://static.example.org/file.txt');
        $mock->shouldReceive('url')->with('file.txt')->andReturn('https://static.example.org/file.txt');

        $this->assertEquals('https://static.example.org/file.txt', $adapter->getUrl('/file.txt'));
        $this->assertEquals('https://static.example.org/file.txt', $adapter->getUrl('file.txt'));
    }

    /**
     * @dataProvider selectelProvider
     */
    public function testWrite($adapter, $mock)
    {
        $mock->shouldReceive('uploadFromString')->andReturn(md5('test'));

        $result = $adapter->write('something', 'contents', new Config());
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals('file', $result['type']);
    }

    /**
     * @dataProvider selectelProvider
     */
    public function testUpdate($adapter, $mock)
    {
        $mock->shouldReceive('uploadFromString')->andReturn(md5('test'));

        $result = $adapter->update('something', 'contents', new Config());
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals('file', $result['type']);
    }

    /**
     * @dataProvider selectelProvider
     */
    public function testWriteStream($adapter, $mock)
    {
        $mock->shouldReceive('uploadFromStream')->andReturn(md5('test'));

        $file = tmpfile();
        $result = $adapter->writeStream('something', $file, new Config());
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals('file', $result['type']);
        fclose($file);
    }

    /**
     * @dataProvider selectelProvider
     */
    public function testUpdateStream($adapter, $mock)
    {
        $mock->shouldReceive('uploadFromStream')->andReturn(md5('test'));

        $file = tmpfile();
        $result = $adapter->updateStream('something', $file, new Config());
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals('file', $result['type']);
        fclose($file);
    }

    /**
     * @dataProvider selectelProvider
     */
    public function testRead($adapter, $mock, $files)
    {
        $file = Mockery::mock('ArgentCrusade\Selectel\CloudStorage\File');
        $file->shouldReceive('read')->andReturn('something');
        $files->shouldReceive('find')->andReturn($file);

        $result = $adapter->read('something');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('contents', $result);
    }

    /**
     * @dataProvider selectelProvider
     */
    public function testReadStream($adapter, $mock, $files)
    {
        $stream = tmpfile();
        fwrite($stream, 'something');

        $file = Mockery::mock('ArgentCrusade\Selectel\CloudStorage\File');
        $file->shouldReceive('readStream')->andReturn($stream);
        $files->shouldReceive('find')->andReturn($file);

        $result = $adapter->readStream('something');
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('stream', $result);
        $this->assertEquals('something', fread($result['stream'], 1024));

        fclose($stream);
    }

    /**
     * @dataProvider selectelProvider
     */
    public function testRename($adapter, $mock, $files)
    {
        $file = Mockery::mock('ArgentCrusade\Selectel\CloudStorage\File');
        $file->shouldReceive('rename')->andReturn('newpath');
        $files->shouldReceive('find')->andReturn($file);

        $this->assertTrue($adapter->rename('oldpath', 'newpath'));
    }

    /**
     * @dataProvider selectelProvider
     */
    public function testCopy($adapter, $mock, $files)
    {
        $file = Mockery::mock('ArgentCrusade\Selectel\CloudStorage\File');
        $file->shouldReceive('copy')->andReturn('newpath');
        $files->shouldReceive('find')->andReturn($file);

        $this->assertTrue($adapter->copy('from', 'to'));
    }

    /**
     * @dataProvider selectelProvider
     */
    public function testCreateDir($adapter, $mock)
    {
        $mock->shouldReceive('createDir')->andReturn(md5('test'));
        $result = $adapter->createDir('something', new Config());

        $this->assertInternalType('array', $result);
    }

    /**
     * @dataProvider selectelProvider
     */
    public function testDelete($adapter, $mock, $files)
    {
        $file = Mockery::mock('ArgentCrusade\Selectel\CloudStorage\File');
        $file->shouldReceive('delete')->andReturn(true);
        $files->shouldReceive('find')->andReturn($file);

        $mock->shouldReceive('deleteDir')->andReturn(true);

        $this->assertTrue($adapter->delete('something'));
        $this->assertTrue($adapter->deleteDir('something'));
    }
}
