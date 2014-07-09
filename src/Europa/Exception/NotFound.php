<?php

namespace Europa\Exception;;

class NotFound extends ExceptionAbstract
{
    protected $httpCode = 404;

    protected $request;

    public function __construct($request) {
        $this->request = $request;
        parent::__construct();
    }
}