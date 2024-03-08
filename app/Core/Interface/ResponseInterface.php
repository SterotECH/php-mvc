<?php

namespace App\Core\Interface;

interface ResponseInterface
{
    public function toJson();

    public function getResponseData();
}