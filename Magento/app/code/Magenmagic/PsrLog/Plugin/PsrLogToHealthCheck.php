<?php


namespace Magenmagic\PsrLog\Plugin;

use Magenmagic\HealthCheck\Api\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class PsrLogToHealthCheck
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

    public function afterEmergency(\Psr\Log\LoggerInterface $subject, $result, $message, $context)
    {
        if($this->isEnabled()) {
            $this->healthCheckLogger->log('EMERGENCY', json_encode(['message'=> $message, 'context'=> $context]));
        }
    }

    public function afterAlert(\Psr\Log\LoggerInterface $subject, $result, $message, $context)
    {
        if($this->isEnabled()) {
            $this->healthCheckLogger->log('ALERT', json_encode(['message'=> $message, 'context'=> $context]));
        }
    }

    public function afterCritical(\Psr\Log\LoggerInterface $subject, $result, $message, $context)
    {
        if($this->isEnabled()) {
            $this->healthCheckLogger->log('CRITICAL', json_encode(['message'=> $message, 'context'=> $context]));
        }
    }

    public function afterError(\Psr\Log\LoggerInterface $subject, $result, $message, $context)
    {
        if($this->isEnabled()) {
            $this->healthCheckLogger->log('ERROR', json_encode(['message'=> $message, 'context'=> $context]));
        }
    }

    public function afterWarning(\Psr\Log\LoggerInterface $subject, $result, $message, $context)
    {
        if($this->isEnabled()) {
            $this->healthCheckLogger->log('WARNING', json_encode(['message'=> $message, 'context'=> $context]));
        }
    }

    public function afterNotice(\Psr\Log\LoggerInterface $subject, $result, $message, $context)
    {
        if($this->isEnabled()) {
            $this->healthCheckLogger->log('WARNING', json_encode(['message'=> $message, 'context'=> $context]));
        }
    }

    public function afterInfo(\Psr\Log\LoggerInterface $subject, $result, $message, $context)
    {
        if($this->isEnabled()) {
            $this->healthCheckLogger->log('WARNING', json_encode(['message'=> $message, 'context'=> $context]));
        }
    }

    public function afterDebug(\Psr\Log\LoggerInterface $subject, $result, $message, $context)
    {
        if($this->isEnabled()) {
            $this->healthCheckLogger->log('WARNING', json_encode(['message'=> $message, 'context'=> $context]));
        }
    }
}
