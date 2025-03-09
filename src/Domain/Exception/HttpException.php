<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Exception;

class HttpException extends Exception
{
    private $statusCode;

    public function __construct($statusCode, $message = '', $code = 0, ?Exception $previous = null)
    {
        $this->statusCode = $statusCode;
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
