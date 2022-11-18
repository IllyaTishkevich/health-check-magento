<?php


namespace Magenmagic\SalesCheck\Observer;

use Magenmagic\SalesCheck\Helper\Data;
use Magenmagic\SalesCheck\Helper\Data as ConfigData;
use Magenmagic\SalesCheck\Model\Config\Options;
use Magento\Framework\Event\Observer;

class PlaceOrder implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var ConfigData
     */
    protected $config;

    /**
     * @var \Magenmagic\SalesCheck\Model\Sales\Order\Data
     */
    protected $order;

    /**
     * @var \Magenmagic\HealthCheck\Api\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magenmagic\HealthCheck\Logger\Logger
     */
    protected $hcLog;

    /**
     * PlaceOrder constructor.
     * @param ConfigData $config
     * @param \Magenmagic\SalesCheck\Model\Sales\Order\Data $order
     * @param \Magenmagic\HealthCheck\Api\LoggerInterface $logger
     * @param \Magenmagic\HealthCheck\Logger\Logger $hcLog
     */
    public function __construct(
        \Magenmagic\SalesCheck\Helper\Data $config,
        \Magenmagic\SalesCheck\Model\Sales\Order\Data $order,
        \Magenmagic\HealthCheck\Api\LoggerInterface $logger,
        \Magenmagic\HealthCheck\Logger\Logger $hcLog
    ) {
        $this->config = $config;
        $this->order = $order;
        $this->logger = $logger;
        $this->hcLog = $hcLog;
    }

    public function execute(Observer $observer)
    {
        if ($this->config->getStoreConfig(Data::GENERAL_ENABLED) &&
            $this->config->getStoreConfig(Data::SALES_ENABLED) &&
            ($this->config->getStoreConfig(Data::SEND_RULE) === Options::OPTION_ORDER_PLACE)
        ) {
            try {
                $order = $observer->getOrder();

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
                    'date' => date('Y-m-d H:i:s', time()),
                    'item_count' => $order->getTotalItemCount(),
                    'items' => $items
                ];

                $this->config->setStoreConfig(ConfigData::LAST_SEND_TIME, date('Y-m-d H:i:s', time()));

                $this->logger->log(
                    $this->config->getStoreConfig(
                        Data::LOG_LEVEL),
                    json_encode($ordersData));
            } catch (\Exception $e) {
                $this->hcLog->error($e->getMessage());
            }
        }
    }
}
