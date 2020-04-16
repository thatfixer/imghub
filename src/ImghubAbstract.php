<?php

namespace Imghub;

use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\PhpFileCache;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Imghub\Exceptions\BadCacheDirectoryException;
use Imghub\Exceptions\FileNotFoundException;
use Imghub\Exceptions\OutMimeLimitException;
use Imghub\Exceptions\OutSizeLimitException;

abstract class ImghubAbstract implements ImghubInterface
{
    const USER_AGENT = 'Imghub/0.0.1';

    protected $tokenLifeTime = 0;

    protected $sizeLimit = 0;

    protected $mimeLimit = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];

    protected $tempFile;

    /**
     * @var ClientInterface
     */
    protected static $httpClient;

    /**
     * @var CacheProvider
     */
    protected static $cacheClient;

    public function __construct(ClientInterface $httpClient = null)
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
    }

    /**
     * @param string $cacheDir
     * @return ImghubAbstract
     * @throws BadCacheDirectoryException
     */
    public function setCacheDir($cacheDir)
    {
        if (! is_dir($cacheDir)) {
            throw new BadCacheDirectoryException('Directory not found: ' . $cacheDir);
        }

        if (! is_writeable($cacheDir)) {
            throw new BadCacheDirectoryException('Directory not writeable: ' . $cacheDir);
        }

        self::$cacheClient = new PhpFileCache($cacheDir);
        return $this;
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