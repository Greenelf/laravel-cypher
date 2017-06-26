<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 26.06.17
 * Time: 12:31
 */
namespace Greenelf\LaravelCypher\Exceptions;
use Exception;

class CypherException extends Exception
{
    public function __construct(
        $exceptionType = "",
        $message = "",
        $code = 0,
        Throwable $previous = null
    ) {
        $message = $this->$exceptionType();
        parent::__construct($message, $code, $previous);
    }

    private function typeError()
    {
        return 'Variable is object - error type';
    }
}