<?php

namespace Imghub;

use Doctrine\Common\Cache\PhpFileCache;
use GuzzleHttp\Client;
use Imghub\Exceptions\FileNotFoundException;
use Imghub\Exceptions\OutMimeLimitException;
use Imghub\Exceptions\OutSizeLimitException;

abstract class ImghubAbstract implements ImghubInterface
{
    const USER_AGENT = 'Imghub/0.0.1';

    public $tokenLifeTime = 0;

    public $sizeLimit = 0;

    public $mimeLimit = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];

    public $tempFile;

    public static $httpClient;

    public static $cacheClient;

    public function __construct($httpClient = null, $cacheClient = null)
    {
        if (is_null(self::$httpClient)) {
            self::$httpClient = ! is_null($httpClient)
                ? $httpClient
                : new Client([
                    'timeout' => 60,
                    'headers' => [
                        'User-Agent' => self::USER_AGENT
                    ]
                ]);
        }

        if (is_null(self::$cacheClient)) {
            self::$cacheClient = ! is_null($cacheClient)
                ? $cacheClient
                : new PhpFileCache(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'cache');
        }
    }

    /**
     * @param string $tempFile
     * @return self
     * @throws FileNotFoundException
     * @throws OutSizeLimitException
     * @throws OutMimeLimitException
     */
    public function upload($tempFile)
    {
        if (! is_file($tempFile)) {
            throw new FileNotFoundException('File Not found: ' . $tempFile);
        }

        if ($this->sizeLimit && filesize($tempFile) > $this->sizeLimit) {
            throw new OutSizeLimitException('Size limit: ' . $this->tokenLifeTime . ' kb.');
        }

        $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $tempFile);
        if (! in_array($mime, $this->mimeLimit)) {
            throw new OutMimeLimitException('Mime limit: ' . join('|', $this->mimeLimit));
        }

        $this->tempFile = $tempFile;
        return $this;
    }

    protected function fakeFileName()
    {
        return uniqid() . '.jpg';
    }
}