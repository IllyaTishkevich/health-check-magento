<?php

namespace Magenmagic\SalesCheck\Model\Sales\Order;


class Count extends \Magento\Framework\DataObject {

    private $_salesOrderCollectionFactory;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $ocFactory,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->_salesOrderCollectionFactory = $ocFactory;
    }


    public function getForLastHour()
    {
        $prevTime = date('Y-m-d H:i:s', time() - 3600);

        $orders = $this->_salesOrderCollectionFactory->create()
            ->addFieldToFilter('created_at', ['from' => $prevTime]);

        return $orders->getSize();
    }


}
