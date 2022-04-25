<?php

namespace Magenmagic\SalesCheck\Helper;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    const GENERAL_ENABLED = 'mm_health_check/general/enabled';
    const SALES_ENABLED = 'mm_health_check/sales/enabled';
    const LOG_LEVEL = 'mm_health_check/sales/log_level';
    const LAST_SEND_TIME = 'mm_health_check/sales/time';

    protected $_writer;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        WriterInterface $writer
    ) {
        parent::__construct($context);
        $this->_writer = $writer;
    }


    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    public function setStoreConfig($path, $value)
    {
        $this->_writer->save($path, $value, $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $this->scopeConfig->clean();
    }

}
