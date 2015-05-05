<?php namespace Drpdigital\FluentLogger\Handler;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Fluent\Logger\FluentLogger;

/**
 * Class FluentHandler
 * @package DrpDigital\FluentLogger\Handler
 */
class FluentHandler extends AbstractProcessingHandler
{

    /**
     * @var FluentLogger
     */
    protected $logger;

    /**
     * @var
     */
    private $environment;

    /**
     * @var
     */
    private $version;

    /**
     * @param FluentLogger $logger
     * @param bool|int $level
     * @param bool $bubble
     */
    function __construct(
        FluentLogger $logger,
        $level = Logger::DEBUG,
        $bubble = true
    ) {

        parent::__construct($level, $bubble);

        $this->logger = $logger;

    }

    /**
     * @param mixed $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {

        $tag = $this->environment . '.' . $record['message'];

        $data['laravel_environment'] = $this->environment;

        $data['laravel_version'] = $this->version;

        $data['level'] = Logger::getLevelName($record['level']);

        $this->logger->post($tag, $data);
    }

}
