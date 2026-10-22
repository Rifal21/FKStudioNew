<?php

namespace App\Services\Waha;

use RuntimeException;
use Throwable;

class WahaException extends RuntimeException
{
    public function __construct(string $message = '', public readonly ?int $status = null, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
