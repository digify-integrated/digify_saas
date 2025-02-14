<?php

namespace App\Middlewares;

interface MiddlewareInterface {
    /**
     * Handle the request and determine whether to proceed.
     *
     * @return bool Returns true to continue request execution, false to halt execution.
     */
    public function handle();
}