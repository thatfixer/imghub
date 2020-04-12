<?php

namespace Imghub\Hubs;

use Imghub\Exceptions\BadResponseException;
use Imghub\ImghubAbstract;

class Baidu extends ImghubAbstract
{
    const URL_UPLOAD_FILE = 'http://baike.baidu.com/api/common/uploadimage';

    public $sizeLimit = 10485760; //10MB

    public $mimeLimit = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/pjpeg',
        'image/x-png',
        'image/webp',
        'application/octet-stream',
    ];

    /**
     * @return string
     * @throws BadResponseException
     */
    public function url()
    {
        $response = self::$httpClient->post(self::URL_UPLOAD_FILE, [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($this->tempFile, 'r'),
                    'filename' => $this->fakeFileName(),
                ],
                [
                    'name' => 'echo',
                    'contents' => 1,
                ]
            ]
        ]);

        $data = json_decode($response->getBody());
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadResponseException('Get url failed: ' . $response->getBody());
        }

        return $data->picUrl;
    }
}