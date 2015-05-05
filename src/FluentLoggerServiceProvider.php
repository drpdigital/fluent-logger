<?php namespace Drpdigital\FluentLogger;

use Drpdigital\FluentLogger\Handler\FluentHandler;
use Drpdigital\FluentLogger\Writer\Writer;
use Illuminate\Support\ServiceProvider;
use Fluent\Logger\FluentLogger;

/**
 * Class FluentLoggerServiceProvider
 * @package Drpdigital\FluentLogger
 */
class FluentLoggerServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = false;

    /**
     * {@inheritdoc}
     */
    public function register()
    {

        $this->package('drpdigital/fluent-logger');

        $this->app->config->package('drpdigital/fluent-logger', realpath(__DIR__ . '/../../config'), 'fluent-logger');

    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

        if (!$this->app->config->get('fluent-logger::enabled')) {
            return;
        }

        //Bind the FluentLogger to the application
        $this->app->singleton('log.fluent.logger', function ($app) {
            //Get the logger config options
            $host = $app->config->get('fluent-logger::host', FluentLogger::DEFAULT_ADDRESS);
            $port = $app->config->get('fluent-logger::port', FluentLogger::DEFAULT_LISTEN_PORT);
            $options = $app->config->get('fluent-logger::options');

            return new FluentLogger($host, $port, $options);

        });

        //Get the default error logging level
        $level = $this->app->config->get('fluent-logger::level', 'error');

        $this->app->log = new Writer($this->app->log->getMonolog());

        $this->app->log->registerHandler(
            $level,
            function ($level) {
                $app = $this->app;

                $handler = new FluentHandler($app['log.fluent.logger'], $level);

                $handler->setEnvironment($app->environment());
                $handler->setVersion($app::VERSION);

                return $handler;
            }
        );

    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [
            'log.fluent.logger'
        ];
    }

}
