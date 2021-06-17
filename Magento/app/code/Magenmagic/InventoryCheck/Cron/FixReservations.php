<?php
/**
 *
 *  * @author MagenMagic Team
 *  * @copyright Copyright (c) 2021 MagenMagic (https://www.magenmagic.com)
 *  * @package
 *
 */

namespace Magenmagic\InventoryCheck\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\OrderRepository;
use Magenmagic\InventoryCheck\Helper\Data;
use Psr\Log\LoggerInterface;

class FixReservations
{
    private $helper;

    private $orderRepository;

    private $searchCriteriaBuilder;

    private $logger;

    public function __construct(
        Data $helper,
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        LoggerInterface $logger
    ) {
        $this->helper                = $helper;
        $this->orderRepository       = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->logger             = $logger;
    }

    public function execute()
    {
        if ($this->helper->isModuleOutputEnabled('Magenmagic_InventoryCheck')
            && $this->helper->isEnabled()
            && $this->helper->getStoreConfig('mm_cron_jobs/mmcheckinventory/crontasks/cron_active')) {
            $this->logger->info('Start: ' . __METHOD__ . ' ' . date("F j, Y, g:i a"));
            $start     = microtime(true);
            $daysDepth = (int)$this->helper->getStoreConfig('mm_cron_jobs/mmcheckinventory/crontasks/cron_days_depth')
                ? (int)$this->helper->getStoreConfig('mm_cron_jobs/mmcheckinventory/crontasks/cron_days_depth') : 1;
            $dryRun    = $this->helper->getStoreConfig('mm_cron_jobs/mmcheckinventory/crontasks/cron_active');
            $storeIds  =
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
            $time = 'Executing time: ' . __METHOD__ . ' ' . round(microtime(true) - $start, 4) . 's.';
            $this->logger->info($time);
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
}
