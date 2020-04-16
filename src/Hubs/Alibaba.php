<?php

namespace Imghub\Hubs;

use Imghub\Exceptions\BadResponseException;
use Imghub\ImghubAbstract;

class Alibaba extends ImghubAbstract
{
    const URL_UPLOAD_FILE = 'https://kfupload.alibaba.com/mupload';

    protected $sizeLimit = 5242880; //5MB

    /**
     * @return string
     * @throws BadResponseException
     */
    public function url()
    {
        if ($this->tempFile) {
            throw new BadResponseException('Please upload file.');
        }

        $response = self::$httpClient->post(self::URL_UPLOAD_FILE, [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($this->tempFile, 'r'),
                    'filename' => $this->fakeFileName(),
                ],
                [
                    'name' => 'name',
                    'contents' => $this->fakeFileName(),
                ],
                [
                    'name' => 'scene',
                    'contents' => 'aeMessageCenterV2ImageRule',
                ]
            ]
        ]);

        $data = json_decode($response->getBody());
        if (json_last_error() !== JSON_ERROR_NONE || $data->code != 0) {
            throw new BadResponseException('Get url failed: ' . $response->getBody());
        }

        return $data->url;
    }
}