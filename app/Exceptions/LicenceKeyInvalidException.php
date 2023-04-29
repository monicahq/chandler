<?php

namespace App\Exceptions;

use Exception;

class LicenceKeyInvalidException extends Exception
{
    public function __construct($message = '')
    {
        $message = trans('app.exception_licence_key_invalid');
        parent::__construct($message);
    }
}
