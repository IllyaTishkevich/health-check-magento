<?php

namespace Magenmagic\SalesCheck\Helper;


class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    public function __construct(
        \Magento\Framework\App\Helper\Context $context)
    {
        parent::__construct($context);
    }


    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }


}
