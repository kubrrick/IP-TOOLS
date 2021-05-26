<?php

namespace kubrick;

class WrongIPv6FormatException extends \Exception
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString():string
    {
        return parent::getMessage();
    }
}