<?php

namespace LaravelCms\Form\Contracts;

use LaravelCms\Form\Repository;

interface Actionable
{
    /**
     * @param Repository $repository
     *
     * @return mixed
     */
    public function build(Repository $repository);
}
