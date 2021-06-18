<?php


namespace Magenmagic\PsrLog\Plugin;

use Magenmagic\HealthCheck\Api\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class PsrLogToHealthCheck //extends \Magento\Framework\Logger\Monolog
{
    private $healthCheckLogger;
    private $scopeConfig;

    public function __construct(
        LoggerInterface $healthCheckLogger,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->healthCheckLogger = $healthCheckLogger;
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue('magenmagic_healthcheck_psrlog/general/enable');
    }

    public function beforeAddRecord(\Psr\Log\LoggerInterface $subject, $level, $message, $context) {
        if($this->isEnabled()) {
            $levelName = \Monolog\Logger::getLevelName($level);
            $this->healthCheckLogger->log($levelName, json_encode(['message'=> $message, 'context'=> $context]));
        }
    }
}
