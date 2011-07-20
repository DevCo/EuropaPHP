<?php

namespace Europa\Bootstrapper;

/**
 * The most basic implementation of a bootstrapper.
 *
 * @category Bootstrapping
 * @package  Bootstrapper
 * @author   Trey Shugart <treshugart@gmail.com>
 * @license  Copyright (c) 2011 Trey Shugart http://europaphp.org/license
 */
interface BootstrapperInterface
{
    /**
     * Bootstraps the application
     * 
     * @return void
     */
    public function __invoke();
}