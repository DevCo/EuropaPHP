<?php
namespace Europa\Exception;
use Europa\Filter\CamelCaseSplitFilter;

/**
 * Base exception class.
 * 
 * @category Exceptions
 * @package  Lighthouse
 * @author   Trey Shugart <tshugart@ultraserve.com.au>
 * @license  Copyright (c) Ultra Serve http://ultraserve.com.au/license
 */
abstract class ExceptionAbstract extends \Exception
{
    protected $httpCode = 500;

    /**
     * Sets up the exception.
     * 
     * @param string $message The error message.
     * @param mixed  $code    The error code.
     * 
     * @return Exception
     */
    public function __construct($message = null, $code = 0)
    {
        parent::__construct(
            $message ? $message : $this->generateMessage(),
            $code    ? $code    : $this->generateCode()
        );
    }
    
    /**
     * Returns the HTTP error code associated with the exception.
     * 
     * @return int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }
    
    /**
     * Returns the name of the exception without the namespace.
     * 
     * @return string
     */
    public function getName()
    {
        $name = get_class($this);
        $name = explode('\\', $name);
        $name = end($name);
        $name = lcfirst($name);
        return $name;
    }
    
    /**
     * Generates and returns an error message based on the exception name.
     * 
     * @return string
     */
    private function generateMessage()
    {
        if ($this->message) {
            return $this->message;
        }

        $msg  = $this->getName();
        $msg  = (new CamelCaseSplitFilter)->__invoke($msg);
        
        array_walk($msg, function(&$item) {
            $item = lcfirst($item);
        });
        
        $msg  = implode(' ', $msg);
        $msg  = ucfirst($msg);
        $msg .= '.';
        
        return $msg;
    }
    
    /**
     * Generates and returns an error code.
     *
     * @return mixed
     */
    private function generateCode()
    {
        $code = $this->getName();
        $code = crc32($code);
        return $code;
    }
}