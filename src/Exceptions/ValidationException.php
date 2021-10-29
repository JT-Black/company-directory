<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    private $errors;

    public function __construct ($errors = null)
    {
        parent::__construct("Validation Error", 422);
        $this->errors = $errors;
    }
    public function getErrors()
    {
        return $this->errors;
    }

}
