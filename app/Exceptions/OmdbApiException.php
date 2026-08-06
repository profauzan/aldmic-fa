<?php

namespace App\Exceptions;

use RuntimeException;

class OmdbApiException extends RuntimeException
{
    protected $statusCode;

    public function __construct($message, $statusCode = 502)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
    }

    public function statusCode()
    {
        return $this->statusCode;
    }
}
