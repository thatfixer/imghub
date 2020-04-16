<?php

namespace Imghub\Hubs;

use Imghub\Exceptions\BadResponseException;
use Imghub\ImghubAbstract;

class Netease extends ImghubAbstract
{
    const HOST_GET_TOKEN  = 'https://xyq.cbg.163.com';
    const URL_GET_TOKEN   = self::HOST_GET_TOKEN . '/cgi-bin/suggest.py?act=get_upload_image_token';
    const URL_UPLOAD_FILE = 'https://file.webapp.163.com/cbg/file/new/';

    const TOKEN_CACHE_KEY = 'netease.token';

    protected $tokenLifeTime = 300; //5minute

    protected $sizeLimit = 3145728; //3MB

    /**
     * @return string
     * @throws BadResponseException
     */
    public function url()
    {
        if (! $this->tempFile) {
            throw new BadResponseException('Please upload a file');
        }

        $token = $this->getToken();

        $response = self::$httpClient->post(self::URL_UPLOAD_FILE, [
            'headers' => [
                'Accept' => 'application/json',
                'Origin' => self::HOST_GET_TOKEN,
                'User-Agent' => self::USER_AGENT,
            ],
            'multipart' => [
                [
                    'name' => 'fpfile',
                    'contents' => fopen($this->tempFile, 'r'),
                    'filename' => $this->fakeFileName(),
                ],
                [
                    'name' => 'Authorization',
                    'contents' => $token,
                ]
            ]
        ]);

        $data = json_decode($response->getBody());
        if (json_last_error() !== JSON_ERROR_NONE || $data->status != 200) {
            throw new BadResponseException('Get url failed: ' . $response->getBody());
        }

        return json_decode($data->body)->url;
    }

    /**
     * @return string
     * @throws BadResponseException
     */
    protected function getToken()
    {
        $token = self::$cacheClient ? self::$cacheClient->fetch(self::TOKEN_CACHE_KEY) : null;

        if (! $token) {
            $response = self::$httpClient->get(self::URL_GET_TOKEN);
            $data = json_decode($response->getBody());
            if (json_last_error() !== JSON_ERROR_NONE || $data->status != 1) {
                throw new BadResponseException('Get token failed：' . $response->getBody());
            }

            $token = $data->token;
            self::$cacheClient && self::$cacheClient->save(self::TOKEN_CACHE_KEY, $token, $this->tokenLifeTime);
        }

        return $token;
    }
}