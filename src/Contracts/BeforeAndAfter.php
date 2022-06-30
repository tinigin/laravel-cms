<?php

namespace LaravelCms\Contracts;

interface BeforeAndAfter {
    /**
     * Run this action before main action
     *
     * @return boolean
     */
    public function before();

    /**
     * Run this action after main action
     *
     * @return boolean
     */
    public function after();
}
