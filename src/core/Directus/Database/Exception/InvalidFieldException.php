<?php

namespace Directus\Database\Exception;

use Directus\Exception\UnprocessableEntity;

class InvalidFieldException extends UnprocessableEntity
{
    const ERROR_CODE = 202;

    public function __construct($field)
    {
        $message = sprintf('Invalid field "%s"', $field);

        parent::__construct($message);
    }
}
