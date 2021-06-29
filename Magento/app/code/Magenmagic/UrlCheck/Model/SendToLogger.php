<?php

namespace Magenmagic\UrlCheck\Model;

class SendToLogger
{
    private $loggerHealthCheck;

    private $helper;

    public function __construct(
        \Magenmagic\HealthCheck\Api\LoggerInterface $loggerHealthCheck,
        \Magenmagic\UrlCheck\Helper\Config $helper
    )
    {
        $this->loggerHealthCheck = $loggerHealthCheck;
        $this->helper = $helper;
    }

    public function send($result)
    {
        $result = json_encode($result);
        $logLevel = $this->helper->getLogLevel();
        $this->loggerHealthCheck->log($logLevel, $result);
    }
}
