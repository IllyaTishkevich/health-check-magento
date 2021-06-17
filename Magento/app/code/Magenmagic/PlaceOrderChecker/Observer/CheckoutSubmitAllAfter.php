<?php


namespace Magenmagic\PlaceOrderChecker\Observer;

use Magenmagic\PlaceOrderChecker\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CheckoutSubmitAllAfter implements ObserverInterface
{
    protected $logger;

    private $resource;
    private $helper;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resource,
        Data $helper
    ) {
        $this->logger = $logger;
        $this->resource = $resource;
        $this->helper = $helper;
    }

    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getData('order');
            $orderId = $order->getData('id');
            $quoteId = $order->getData('quote_id');
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $connection = $this->resource->getConnection('core_write');

            $query = "UPDATE  mm_po_checker SET is_create = '$orderId' WHERE  quote_id = '$quoteId' AND ip = '$ip' AND sended_to_healthcheck = 0;";
            $connection->exec($query);
            if($this->helper->isEnabled() && $this->helper->isCheckImmediately()) {
                $entities = $this->checkerResourceModel->getList();
                if ($entities)
                {
                    foreach ($entities as $entity) {
                        $this->sendToHealthCheckLogger->send($entity);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
