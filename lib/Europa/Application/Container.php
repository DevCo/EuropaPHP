<?php

namespace Europa\Application;
use Europa\Filter\FilterInterface;
use Europa\Filter\UpperCamelCaseFilter;

/**
 * The service injection container represents a collection of configured dependencies. Dependencies are instances
 * of \Europa\Application\Service that represent an object instance. The container provides a fluent interface for
 * accessing dependencies so that they can easily be configured.
 * 
 * @category ServiceInjection
 * @package  Europa
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
class Container
{
    /**
     * The default container instance name.
     * 
     * @var string
     */
    const DEFAULT_INSTANCE_NAME = 'default';
    
    /**
     * Cached service instances.
     * 
     * @var array
     */
    private $deps = array();
    
    /**
     * Filter used for name formatting.
     * 
     * @var \Europa\Filter\FilterInterface
     */
    private $filter;
    
    /**
     * Container instances for static retrieval.
     * 
     * @var array
     */
    private static $containers = array();
    
    /**
     * Sets up the container.
     * 
     * @return \Europa\Application\Container
     */
    public function __construct()
    {
        $this->setFilter(new UpperCamelCaseFilter);
    }
    
    /**
     * Magic caller for resolve($name, $args).
     * 
     * @param string $name  The name of the service.
     * @param mixed  $value The service to register.
     * 
     * @see \Europa\Application\Service::register()
     */
    public function __call($name, array $args = array())
    {
        return $this->resolve($name)->configure($args);
    }
    
    /**
     * Magic caller for register().
     * 
     * @see \Europa\Application\Service::register()
     */
    public function __set($name, $value)
    {
        return $this->register($name, $value);
    }
    
    /**
     * Magic caller for resolve($name).
     * 
     * @see \Europa\Application\Service::resolve()
     */
    public function __get($name)
    {
        return $this->resolve($name);
    }
    
    /**
     * Magic caller for isRegistered($name).
     * 
     * @see \Europa\Application\Service::isRegistered()
     */
    public function __isset($name)
    {
        return $this->isRegistered($name);
    }
    
    /**
     * Magic caller for unregister($name).
     * 
     * @see \Europa\Application\Service::unregister()
     */
    public function __unset($naem)
    {
        return $this->unregister($name);
    }
    
    /**
     * Creates a service if it doesn't already exist and returns it.
     * 
     * @param string $name The name of the service.
     * 
     * @return \Europa\Application\Service
     */
    public function resolve($name)
    {
        if (!isset($this->deps[$name])) {
            $dep = $this->getClassNameFor($name);
            $dep = new Service($dep);
            $this->deps[$name] = $dep;
        }
        return $this->deps[$name];
    }
    
    /**
     * Returns a new instance of a configured service.
     * 
     * @param string $name The name of the service.
     * @param array  $args The arguments to pass to the new instance.
     * 
     * @return mixed
     */
    public function createService($name, array $args = array())
    {
        return $this->resolve($name)->configure($args)->create();
    }
    
    /**
     * Returns a configured instance of the specified service.
     * 
     * @param string $name The name of the service.
     * @param array  $args The arguments to pass if creating a new instance.
     * 
     * @return mixed
     */
    public function getService($name, array $args = array())
    {
        return $this->resolve($name)->configure($args)->get();
    }
    
    /**
     * Detects the value of $value and handles it appropriately.
     *   - Instances of \Europa\Application\Service are registered on the container.
     *   - Other instances are created as a service then registered.
     * 
     * @param string                      $name    The name of the service.
     * @param \Europa\Application\Service $service One of many allowed values.
     * 
     * @return \Europa\Application\Container
     */
    public function register($name, Service $service)
    {
        $this->deps[$name] = $service;
        return $this;
    }
    
    /**
     * Returns whether or not the specified service is registered.
     * 
     * @param string $name The service name.
     * 
     * @return bool
     */
    public function isRegistered($name)
    {
        return isset($this->deps[$name]);
    }
    
    /**
     * Removes the specified service.
     * 
     * @param string $name The service name.
     * 
     * @return \Europa\Application\Container
     */
    public function unRegister($name)
    {
        if (!isset($this->deps[$name])) {
            unset($this->deps[$name]);
        }
        return $this;
    }
    
    /**
     * Sets a filter to use for converting a service name into a class name.
     * 
     * @param \Europa\Filter\FilterInterface $filter The filter to use for name formatting.
     * 
     * @return \Europa\Application\Container
     */
    public function setFilter(FilterInterface $filter)
    {
        $this->filter = $filter;
        return $this;
    }
    
    /**
     * Returns the class name for the specified service.
     * 
     * @param string $name The name of the service to get the class name for.
     * 
     * @return string
     */
    private function getClassNameFor($name)
    {
        return $this->filter->filter($name);
    }
    
    /**
     * Registers the container as an instance.
     * 
     * @param string $name      The instance name.
     * @param self   $container The container to register.
     * 
     * @return void
     */
    public static function set($name, self $container)
    {
    	self::$containers[$name] = $container;
    }
    
    /**
     * Returns an instance of a container.
     * 
     * @param string $name The instance name to get if using multiple instances.
     * 
     * @return \Europa\Application\Container
     */
    public static function get($name = null)
    {
    	$name = $name ? $name : self::DEFAULT_INSTANCE_NAME;
        if (!isset(self::$containers[$name])) {
            self::$containers[$name] = new static;
        }
        return self::$containers[$name];
    }
}
