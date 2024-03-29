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

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface  $scopeConfig,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->encryptor = $encryptor;
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

    public function getApiLogUrl()
    {
        return $this->getUrl() . '/api/log';
    }

    public function getKey()
    {
        return $this->scopeConfig->getValue('mm_health_check/general/key',
            ScopeInterface::SCOPE_WEBSITE);
    }

    public function getTimeoutValue()
    {
        return $this->scopeConfig->getValue('mm_health_check/general/timeout',
        ScopeInterface::SCOPE_WEBSITE);
    }

    public function getLogin()
    {
        return $this->scopeConfig->getValue('mm_health_check/general/login',
            ScopeInterface::SCOPE_WEBSITE);
    }

    public function getPassword()
    {
        $hash = $this->scopeConfig->getValue('mm_health_check/general/password',
            ScopeInterface::SCOPE_WEBSITE);

        return $this->encryptor->decrypt($hash);
    }
}
