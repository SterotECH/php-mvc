<?php

namespace App\Core\Exceptions;

use Throwable;

class ConfigNotFoundException extends \Exception
{
    /**
     * __construct
     *
     * @param  mixed $filename
     * @param  mixed $configDir
     * @return void
     */
    public function __construct(string $filename, string $configDir)
    {
        $message = "Config file '$filename' not found in '$configDir'";
        parent::__construct($message);
    }
}
