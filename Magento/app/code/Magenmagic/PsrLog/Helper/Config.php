<?php


namespace Magenmagic\PsrLog\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue('mm_health_check/psrlog/enable');
    }

    public function getCodes()
    {
        return $this->scopeConfig->getValue('mm_health_check/psrlog/codes');
    }
}
