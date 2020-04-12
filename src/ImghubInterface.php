<?php

namespace Imghub;

interface ImghubInterface
{
    /**
     * @param string $tempFile
     * @return static
     */
    public function upload($tempFile);

    /**
     * @return string
     */
    public function url();
}