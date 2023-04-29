<?php

namespace App\Exceptions;

use Exception;

class LicenceKeyDontExistException extends Exception
{
    public function __construct($message = '')
    {
        $message = trans('app.exception_licence_key_dont_exist');
        parent::__construct($message);
    }
}
