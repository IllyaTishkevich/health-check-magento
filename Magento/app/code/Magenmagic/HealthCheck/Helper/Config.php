<?php


namespace Magenmagic\HealthCheck\Helper;

use \Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    public function isEnable()
    {
        return $this->scopeConfig->getValue('mm_health_check/general/enabled',
            ScopeInterface::SCOPE_WEBSITE);
    }

    public function getUrl()
    {
        return $this->scopeConfig->getValue('mm_health_check/general/api_url',
            ScopeInterface::SCOPE_WEBSITE);
    }

    public function getKey()
    {
        return $this->scopeConfig->getValue('mm_health_check/general/key',
            ScopeInterface::SCOPE_WEBSITE);
    }
}
