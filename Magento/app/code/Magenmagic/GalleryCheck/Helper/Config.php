<?php

namespace Magenmagic\GalleryCheck\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const EXTENSION_KEY     = 'mm_health_check';

    const EXTENSION_ENABLED = 'galler_check/enabled';

    const LOG_DATA = 'galler_check/log_field';

    public function getConfig($key, $store = null)
    {
        return $this->scopeConfig->getValue(self::EXTENSION_KEY . '/' . $key, ScopeInterface::SCOPE_STORE, $store);
    }

    public function getEnabled($store = null)
    {
        return $this->getConfig(self::EXTENSION_ENABLED, $store);
    }

    public function getLog($store = null)
    {
        return $this->getConfig(self::LOG_DATA, $store);
    }
}
