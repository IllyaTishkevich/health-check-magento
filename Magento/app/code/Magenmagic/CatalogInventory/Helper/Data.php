<?php

/**
 * @author    MagenMagic Team
 * @copyright Copyright (c) 2020 MagenMagic (https://www.magenmagic.com)
 * @package Magenmagic_CatalogInventory
 */

namespace Magenmagic\CatalogInventory\Helper;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;


class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    private $storeManager;
    private $logger;
    private $productCollection;
    private $transportBuilder;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        LoggerInterface $logger,
        ProductCollection $productCollection,
        StoreManagerInterface $storeManager
    )
    {
        $this->storeManager      = $storeManager;
        $this->logger            = $logger;
        $this->productCollection = $productCollection;
        $this->transportBuilder  = $transportBuilder;
        parent::__construct($context);
    }


    /*
     * get Current store id
     */

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }


    /*
     * get Current store Info
     */

    public function getStore()
    {
        return $this->storeManager->getStore();
    }


    public function log($message = null)
    {
        if (is_null($message))
        {
            $message = '';

            foreach (debug_backtrace() as $call)
            {
                $message .= "\n" . isset($call['file']) ? $call['file'] : '';
                $message .= isset($call['line']) ? (',' . $call['line']) : '';
                $message .= "\n" . isset($call['class']) ? $call['class'] : '';
                $message .= isset($call['type']) ? $call['type'] : '.';
                $message .= isset($call['function']) ? ($call['function'] . '()') : '';
            }
        }

        if (is_string($message))
        {
            $this->logger->debug($message);
        }
        else
        {
            $this->logger->debug(var_export($message, true));
        }
    }


    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue($field);
    }


    public function getEmailHtml($products)
    {
        $html = '<h3>Configurable Products (IDs) that have In-Stock Simples:</h3><div>';
        foreach ($products as $product)
        {
            $html.= $product['id'].' ('.(implode(', ',$product['child_count'])).')<br/>';
        }
//        $html .= implode(', <br/>', $productIds);
        $html .= '</div>';
        return $html;
    }


    public function sendEmailToAdmin($productIds)
    {
        $emailTo = $this->getConfigValue('mmcataloginventory/settoenable/email_to');
        $templId = "mmcataloginventory_settoenable_email_template";

        $sender = [
            'name'  => $this->getConfigValue('mmcataloginventory/settoenable/email_from_name'),
            'email' => $this->scopeConfig->getValue('trans_email/ident_general/email'),
        ];

        try
        {
            $transport = $this->transportBuilder->setTemplateIdentifier($templId)
                ->setTemplateOptions(
                    [
                        'area'  => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(
                    [
                        'report'  => $this->getEmailHtml($productIds),
                        'subject' => $this->getConfigValue('mmcataloginventory/settoenable/email_subject'),
                    ]
                )
                ->setFrom($sender)
                ->addTo($emailTo)
                ->getTransport();

            $transport->sendMessage();
        }
        catch (\Exception $e)
        {
            $this->logger->critical($e->getMessage());
        }

        return $this;
    }


}
