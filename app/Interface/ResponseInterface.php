<?php

namespace App\Interface;

interface ResponseInterface
{
    public function toJson();

    public function getResponseData();
}