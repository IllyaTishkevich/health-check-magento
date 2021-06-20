<?php

namespace Magenmagic\UrlCheck\Helper;

use \Magento\Store\Model\ScopeInterface;

class Config
{
    private $scopeConfig;
    private $storeManager;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function isEnable()
    {
        return $this->scopeConfig->getValue('mm_health_check/urlcheck/enabled', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getLogLevel()
    {
        return $this->scopeConfig->getValue('mm_health_check/urlcheck/log_level', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getDefaultStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
    
}
