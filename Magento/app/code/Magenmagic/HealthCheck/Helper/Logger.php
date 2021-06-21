<?php


namespace Magenmagic\HealthCheck\Helper;

use Zend\Log\Writer\Stream;
use Zend\Log\Logger as PsrLog;

class Logger
{
    protected $logger;

    public function __construct()
    {
        $writer = new Stream(BP . '/var/log/healthcheck-'.date('Y-m-d').'.log');
        $logger = new PsrLog();
        $logger->addWriter($writer);
        $this->logger = $logger;
    }

    public function log($text)
    {
        $this->logger->log(6, $text);
    }
}
