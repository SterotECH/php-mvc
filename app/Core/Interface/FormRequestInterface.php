<?php

namespace App\Core\Interface;

use App\Core\RequestInterface;

interface FormRequestInterface
{
    public function validate(RequestInterface $request);
}