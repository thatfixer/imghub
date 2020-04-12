<?php

namespace Imghub;

use Imghub\Exceptions\ImghubNotFoundException;

class Imghub
{
    /**
     * @param string $hub
     * @return ImghubAbstract
     * @throws ImghubNotFoundException
     */
    public static function hub($hub)
    {
        $class = 'Imghub\\Hubs\\' . ucfirst($hub);

        if (! class_exists($class)) {
            throw new ImghubNotFoundException('The imghub: [' . $hub . '] not found.');
        }

        return new $class;
    }
}