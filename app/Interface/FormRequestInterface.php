<?php

namespace App\Interface;

use App\Core\RequestInterface;

interface FormRequestInterface
{
    public function validate(RequestInterface $request);
}