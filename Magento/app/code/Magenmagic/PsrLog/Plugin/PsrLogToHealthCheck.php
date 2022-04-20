<?php


namespace Magenmagic\PsrLog\Plugin;

use Magenmagic\HealthCheck\Api\LoggerInterface;
use Magenmagic\PsrLog\Helper\Config;
use Monolog\Logger as Monologger;

class PsrLogToHealthCheck
{
    protected $healthCheckLogger;

    protected $config;

    protected static $levels = [
        Monologger::DEBUG     => 'DEBUG',
        Monologger::INFO      => 'INFO',
        Monologger::NOTICE    => 'NOTICE',
        Monologger::WARNING   => 'WARNING',
        Monologger::ERROR     => 'ERROR',
        Monologger::CRITICAL  => 'CRITICAL',
        Monologger::ALERT     => 'ALERT',
        Monologger::EMERGENCY => 'EMERGENCY',
    ];

    public function __construct(
        LoggerInterface $healthCheckLogger,
        Config $config
    ) {
        $this->healthCheckLogger = $healthCheckLogger;
        $this->config = $config;
    }

    protected function isEnabled()
    {
        return $this->config->isEnabled();
    }

    protected function isMessageMustBeSand($code)
    {
        $array = explode(',', $this->config->getCodes());
        return in_array($code, $array);
    }

    public function afterAddRecord(\Psr\Log\LoggerInterface $subject, $result, int $level, string $message, array $context = [])
    {
        if($this->isEnabled() && $this->isMessageMustBeSand(self::$levels[$level])) {
            $this->healthCheckLogger->log(self::$levels[$level], json_encode(['message'=> $message, 'context'=> $context]));
        }

        return$result;
    }
}
