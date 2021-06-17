<?php
/**
* Copyright © 2021 Mobecls. All rights reserved.
*/
namespace Magenmagic\GitCheck\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_ENABLED = 'mm_health_check/mm_git_check/enabled';
    const XML_PATH_LOGID   = 'mm_health_check/mm_git_check/log_id';
    const XML_PATH_STATUS  = 'mm_health_check/mm_git_check/status';

    protected $storeManager;
    
    protected $configWriter;

    /**
     * Data constructor.
     *
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context, 
        StoreManagerInterface $storeManager,
        WriterInterface $configWriter
    ) {
        $this->storeManager = $storeManager;
        $this->configWriter = $configWriter; 
        parent::__construct($context);
    }
    
    public function getIsEnabled() 
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getLogId() 
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_LOGID,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getStatus() 
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_STATUS,
            ScopeInterface::SCOPE_STORE
        );
    }
    
    public function saveStatus($status) 
    {
        $this->configWriter->save(self::XML_PATH_STATUS, $status);
    }
}
