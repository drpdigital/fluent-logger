<?php namespace Drpdigital\FluentLogger\Writer;

use Closure;
use Illuminate\Log\Writer as LaravelWriter;

/**
 * Class Writer
 * @package Drpdigital\FluentLogger\Writer
 */
class Writer extends LaravelWriter
{

    /**
     * Register the handler to Monolog
     * @param $level
     * @param callable $callback
     * @return bool
     */
    public function registerHandler($level, Closure $callback)
    {
        $level = $this->parseLevel($level);

        $handler = call_user_func($callback, $level);

        $this->getMonolog()->pushHandler($handler);

        return true;
    }
}
