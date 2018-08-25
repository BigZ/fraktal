<?php

namespace App\Exception;


use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class IncludeNotFoundException extends BadRequestHttpException
{
    public function __construct()
    {
        parent::__construct('The property selected for inclusion is not available');
    }
}