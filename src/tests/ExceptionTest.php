<?php

namespace Imghub\Tests;

class ExceptionTest extends ImghubTestCase
{
    /**
     * @expectedException \Imghub\Exceptions\FileNotFoundException
     */
    public function testFileNotFoundException()
    {
        \Imghub\Imghub::hub('baidu')->upload(dirname(__DIR__) . '/data/404.txt')->url();
    }

    /**
     * @expectedException \Imghub\Exceptions\ImghubNotFoundException
     */
    public function testImghubNotFoundException()
    {
        \Imghub\Imghub::hub('badHubName')->upload(dirname(__DIR__) . '/data/example.txt')->url();
    }

    /**
     * @expectedException \Imghub\Exceptions\OutMimeLimitException
     */
    public function testOutMimeLimitException()
    {
        \Imghub\Imghub::hub('alibaba')->upload(dirname(__DIR__) . '/data/example.txt')->url();
    }

    /**
     * @expectedException \Imghub\Exceptions\BadCacheDirectoryException
     */
    public function testBadCacheDirectoryException()
    {
        \Imghub\Imghub::hub('netease')->setCacheDir(DIRECTORY_SEPARATOR)->upload(dirname(__DIR__) . '/data/example.jpg')->url();
    }
}