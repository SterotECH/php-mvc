<?php

namespace App\Core;

 use JetBrains\PhpStorm\NoReturn;

 class Response {
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;

    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;

    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_TEMPORARY_REDIRECT = 307;
    const HTTP_PERMANENT_REDIRECT = 308;

    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_PAYLOAD_TOO_LARGE = 413;
    const HTTP_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_IM_A_TEAPOT = 418;
    const HTTP_MISDIRECTED_REQUEST = 421;
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    const HTTP_LOCKED = 423;
    const HTTP_FAILED_DEPENDENCY = 424;
    const HTTP_TOO_EARLY = 425;
    const HTTP_UPGRADE_REQUIRED = 426;
    const HTTP_PRECONDITION_REQUIRED = 428;
    const HTTP_TOO_MANY_REQUESTS = 429;
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_VARIANT_ALSO_NEGOTIATES = 506;
    const HTTP_INSUFFICIENT_STORAGE = 507;
    const HTTP_LOOP_DETECTED = 508;
    const HTTP_NOT_EXTENDED = 510;
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;

    public static function getStatusMessage(int $statusCode): string
    {
        return match ($statusCode) {
            self::HTTP_CONTINUE => "Continue",
            self::HTTP_SWITCHING_PROTOCOLS => "Switching Protocols",
            self::HTTP_OK => "OK",
            self::HTTP_CREATED => "Created",
            self::HTTP_ACCEPTED => "Accepted",
            self::HTTP_NON_AUTHORITATIVE_INFORMATION => "Non-Authoritative Information",
            self::HTTP_NO_CONTENT => "No Content",
            self::HTTP_RESET_CONTENT => "Reset Content",
            self::HTTP_PARTIAL_CONTENT => "Partial Content",
            self::HTTP_MULTIPLE_CHOICES => "Multiple Choices",
            self::HTTP_MOVED_PERMANENTLY => "Moved Permanently",
            self::HTTP_FOUND => "Found",
            self::HTTP_SEE_OTHER => "See Other",
            self::HTTP_NOT_MODIFIED => "Not Modified",
            self::HTTP_USE_PROXY => "Use Proxy",
            self::HTTP_TEMPORARY_REDIRECT => "Temporary Redirect",
            self::HTTP_PERMANENT_REDIRECT => "Permanent Redirect",
            self::HTTP_BAD_REQUEST => "Bad RequestInterface",
            self::HTTP_UNAUTHORIZED => "Unauthorized",
            self::HTTP_PAYMENT_REQUIRED => "Payment Required",
            self::HTTP_FORBIDDEN => "Forbidden",
            self::HTTP_NOT_FOUND => "Not Found",
            self::HTTP_METHOD_NOT_ALLOWED => "Method Not Allowed",
            self::HTTP_NOT_ACCEPTABLE => "Not Acceptable",
            self::HTTP_PROXY_AUTHENTICATION_REQUIRED => "Proxy Authentication Required",
            self::HTTP_REQUEST_TIMEOUT => "RequestInterface Timeout",
            self::HTTP_CONFLICT => "Conflict",
            self::HTTP_GONE => "Gone",
            self::HTTP_LENGTH_REQUIRED => "Length Required",
            self::HTTP_PRECONDITION_FAILED => "Precondition Failed",
            self::HTTP_PAYLOAD_TOO_LARGE => "Payload Too Large",
            self::HTTP_URI_TOO_LONG => "URI Too Long",
            self::HTTP_UNSUPPORTED_MEDIA_TYPE => "Unsupported Media Type",
            self::HTTP_RANGE_NOT_SATISFIABLE => "Range Not Satisfiable",
            self::HTTP_EXPECTATION_FAILED => "Expectation Failed",
            self::HTTP_IM_A_TEAPOT => "I'm a teapot",
            self::HTTP_MISDIRECTED_REQUEST => "Misdirected RequestInterface",
            self::HTTP_UNPROCESSABLE_ENTITY => "Unprocessable Entity",
            self::HTTP_LOCKED => "Locked",
            self::HTTP_FAILED_DEPENDENCY => "Failed Dependency",
            self::HTTP_TOO_EARLY => "Too Early",
            self::HTTP_UPGRADE_REQUIRED => "Upgrade Required",
            self::HTTP_PRECONDITION_REQUIRED => "Precondition Required",
            self::HTTP_TOO_MANY_REQUESTS => "Too Many Requests",
            self::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE => "RequestInterface Header Fields Too Large",
            self::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS => "Unavailable For Legal Reasons",
            self::HTTP_INTERNAL_SERVER_ERROR => "Internal Server Error",
            self::HTTP_NOT_IMPLEMENTED => "Not Implemented",
            self::HTTP_BAD_GATEWAY => "Bad Gateway",
            self::HTTP_SERVICE_UNAVAILABLE => "Service Unavailable",
            self::HTTP_GATEWAY_TIMEOUT => "Gateway Timeout",
            self::HTTP_VERSION_NOT_SUPPORTED => "HTTP Version Not Supported",
            self::HTTP_VARIANT_ALSO_NEGOTIATES => "Variant Also Negotiates",
            self::HTTP_INSUFFICIENT_STORAGE => "Insufficient Storage",
            self::HTTP_LOOP_DETECTED => "Loop Detected",
            self::HTTP_NOT_EXTENDED => "Not Extended",
            self::HTTP_NETWORK_AUTHENTICATION_REQUIRED => "Network Authentication Required",
            default => "Unknown",
        };
    }

     private array $data = [];

     /**
      * @param array $data
      * @param int $status
      * @return string
      */
     public function json(array $data, int $status = self::HTTP_OK): string
     {
         header("Content-type:application/json");
         http_response_code($status);
         return json_encode($data);
     }

     /**
      * @param string $data
      * @param int $status
      * @return string
      */
     public function plainText(string $data, int $status = self::HTTP_OK): string
     {
         header("Content-Type:text/plain;charset=UTF-8");
         http_response_code($status);
         return $data;
     }

     /**
      * @param string $data
      * @param int $status
      * @return string
      */
     public function plainHtml(string $data, int $status = self::HTTP_OK): string
     {
         header("Content-Type:text/html;charset=UTF-8");
         http_response_code($status);
         return $data;
     }

     /**
      * @param string $url
      * @param int $status
      * @return void
      */
     #[NoReturn] public function redirect(string $url, int $status = self::HTTP_MOVED_PERMANENTLY): void
     {
         header("Location:{$url}");
         http_response_code($status);
         exit();
     }

     /**
      * @param array $data
      * @param int $status
      * @return Response
      */
     public function withErrors(array $data, int $status = self::HTTP_UNPROCESSABLE_ENTITY): Response
     {
         http_response_code($status);
         $this->data = [...$data];

         return $this;
     }

     /**
      * @return string
      *
      */
     public function toJson(): string
     {
         header("Content-type:application/json");
         return json_encode($this->data);
     }

     /**
      * @return array
      */
     public function getResponseData(): array
     {
         return $this->data;
     }
}
