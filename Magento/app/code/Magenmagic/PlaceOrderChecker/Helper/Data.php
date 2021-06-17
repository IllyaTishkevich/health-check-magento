<?php


namespace Magenmagic\PlaceOrderChecker\Helper;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory as ConfigCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

class Data
{
    const XML_LASTCHECK_CONFIG = 'magenmagic_place_order_checker/general/last_check';
    const XML_LASTCRONCHECK_CONFIG = 'magenmagic_place_order_checker/general/last_cron_check';
    const XML_ENABLED_CONFIG = 'magenmagic_place_order_checker/general/enabled';
    const XML_CRONENABLED_CONFIG = 'magenmagic_place_order_checker/general/cron_enabled';
    const XML_LOGLEVEL_CONFIG = 'magenmagic_place_order_checker/general/log_level';
    const XML_CHECKIMMEDIATLE_CONFIG = 'magenmagic_place_order_checker/general/check_immediately';

    private $configCollectionFactory;
    private $scopeConfig;

    public function __construct(
        ConfigCollectionFactory $configFactory,
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter
    )
    {
        $this->configCollectionFactory = $configFactory;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
    }

    public function getLastCheck() {
        $collection = $this->configCollectionFactory->create();
        $collection->addFieldToFilter('scope', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $collection->addFieldToFilter('scope_id', 0);
        $collection->addFieldToFilter('path', ['like' => self::XML_LASTCHECK_CONFIG]);
        return $collection->getFirstItem()->getValue();
    }

    public function getLastCronCheck() {
        $collection = $this->configCollectionFactory->create();
        $collection->addFieldToFilter('scope', ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $collection->addFieldToFilter('scope_id', 0);
        $collection->addFieldToFilter('path', ['like' => self::XML_LASTCRONCHECK_CONFIG]);
        return $collection->getFirstItem()->getValue();
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_ENABLED_CONFIG, ScopeInterface::SCOPE_WEBSITE);
    }

    public function isCheckImmediately()
    {
        return $this->scopeConfig->getValue(self::XML_CHECKIMMEDIATLE_CONFIG, ScopeInterface::SCOPE_WEBSITE);
    }

    public function isCronEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_CRONENABLED_CONFIG, ScopeInterface::SCOPE_WEBSITE);
    }

    public function getLogLevel()
    {
        return $this->scopeConfig->getValue(self::XML_LOGLEVEL_CONFIG, ScopeInterface::SCOPE_WEBSITE);
    }

    public function setData($path, $value) {
        $this->configWriter->save($path, $value, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeId = 0);
    }

    public function setLastCheck($value) {
        $this->setData(self::XML_LASTCHECK_CONFIG, $value);
    }

    public function setLastCronCheck($value) {
        $this->setData(self::XML_LASTCRONCHECK_CONFIG, $value);
    }
}
