<?php

namespace Magenmagic\InventoryCheck\Controller\Adminhtml\DryRun;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\OrderRepository;
use Magenmagic\InventoryCheck\Helper\Data;

class Index extends Action
{
    private $helper;

    private $orderRepository;

    private $searchCriteriaBuilder;

    private $resultJsonFactory;

    public function __construct(
        Context $context, JsonFactory $resultJsonFactory, Data $helper, OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct($context);
        $this->helper                = $helper;
        $this->orderRepository       = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->resultJsonFactory     = $resultJsonFactory;
    }

    public function execute()
    {
        if ($this->helper->isModuleOutputEnabled('Magenmagic_InventoryCheck')
            && $this->helper->isEnabled()
            && $this->helper->getStoreConfig('mm_cron_jobs/mmcheckinventory/crontasks/cron_active')) {
            $start = microtime(true);
            $skus      = [];
            $daysDepth = (int)$this->helper->getStoreConfig('mm_cron_jobs/mmcheckinventory/crontasks/cron_days_depth')
                ? (int)$this->helper->getStoreConfig('mm_cron_jobs/mmcheckinventory/crontasks/cron_days_depth') : 1;
            $dryRun    = true;
            $storeIds =
                explode(',', $this->helper->getStoreConfig('mm_cron_jobs/mmcheckinventory/crontasks/cron_stores_ids'));
            $dateTo    = date('Y-m-d H:i:s');
            $dateFrom  = date('Y-m-d H:i:s', time() - 3600 * 24 * $daysDepth);
            $orderFrom = date('Y-m-d H:i:s', strtotime($dateFrom));
            $orderTo   = date('Y-m-d H:i:s', strtotime($dateTo));
            $orders    = $this->getOrders($orderFrom, $orderTo);
            $ordersIds = $this->getOrdersIds($orders);

            if (count($storeIds) == 0) {
                $storeIds[] = 0;
            }
            foreach ($storeIds as $storeId) {

                $skus = $this->helper->fixInventory($storeId, $dryRun, $ordersIds);
            }
            $time = 'Executing time: ' . round(microtime(true) - $start, 4) . 's.';

            return $this->resultJsonFactory->create()->setData(
                ['result' => implode(',', $skus), 'perfomance' => $time]
            );
        } else {
            return $this->resultJsonFactory->create()->setData(
                ['result' => 'Module ,method not active', 'perfomance' => '']
            );
        }
    }

    private function getOrders($orderFrom, $orderTo)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('created_at', $orderFrom, 'gteq')->addFilter(
            'created_at',
            $orderTo,
            'lteq'
        )->create();
        $items          = $this->orderRepository->getList($searchCriteria)->getItems();

        return $items;
    }

    private function getOrdersIds($orders)
    {
        if (count($orders) == 0) {
            return [];
        }
        $ids = [];
        foreach ($orders as $key => $order) {
            $ids[$key] = $order->getData('increment_id');
        }

        return $ids;
    }

    public function isAllowed($resource, $privilege = null): bool
    {
        return $this->_authorization->isAllowed('Magenmagic_InventoryCheck::admin_config');
    }
}
