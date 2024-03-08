<?php

namespace App\Console\Commands;

use Exception;

class ServeCommand
{
    private static string $host = '127.0.0.1';
    private static int $port = 8000;

    /**
     * @throws Exception
     */
    public static function handle(): void
    {
        $server = sprintf('%s:%d', self::$host, self::$port);
        $root = $_SERVER['DOCUMENT_ROOT'];

        self::displayServerInfo($server, $root);

        exec("php -S $server -t public", $output, $returnCode);

        if ($returnCode !== 0) {
            throw new Exception('Failed to start the server.');
        }
    }


    private static function displayServerInfo(string $server, string $root): void
    {
        echo "Server running at $server" . PHP_EOL;
        echo "Document root is $root" . PHP_EOL;
        echo "Press Ctrl+C to quit." . PHP_EOL;
    }
}
