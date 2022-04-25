<?php

namespace Magenmagic\SalesCheck\Model\Sales\Order;

use \Magenmagic\SalesCheck\Helper\Data as ConfigData;

class Data extends \Magento\Framework\DataObject {

    protected $_salesOrderCollectionFactory;

    protected $_data;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $ocFactory,
        ConfigData $configData,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_salesOrderCollectionFactory = $ocFactory;
        $this->_data = $configData;
    }


    public function getSalesData()
    {
        $ordersData = [];
        $date = $this->_data->getStoreConfig(ConfigData::LAST_SEND_TIME);
        $prevTime = $date ? $date : date('Y-m-d H:i:s', time() - 3600 * 24 * 40);

        $orders = $this->_salesOrderCollectionFactory->create()
            ->addFieldToFilter('created_at', ['from' => $prevTime]);


        /** @var  $order \Magento\Sales\Model\Order */
        foreach ($orders as $order) {
            $items = [];
            foreach ($order->getItems() as $item) {
                $items[] = [
                    'id' => $item->getItemId(),
                    'parent' => $item->getParentItemId(),
                    'name' => $item->getName(),
                    'sku' => $item->getSku(),
                    'count' => $item->getQtyOrdered(),
                    'price' => $item->getBasePrice(),
                    'type' => $item->getProductType(),
                ];
            }
            $ordersData[] = [
                'id' => $order->getId(),
                'increment_id' => $order->getIncrementId(),
                'email' => $order->getCustomerEmail(),
                'name' => $order->getCustomerFirstname(),
                'lastname' => $order->getCustomerLastname(),
                'ip' => $order->getRemoteIp(),
                'total' => $order->getBaseGrandTotal(),
                'date' => $order->getCreatedAt(),
                'item_count' => $order->getTotalItemCount(),
                'items' => $items
            ];
        }

        $this->_data->setStoreConfig(ConfigData::LAST_SEND_TIME, date('Y-m-d H:i:s', time()));

        return $ordersData;
    }


}
