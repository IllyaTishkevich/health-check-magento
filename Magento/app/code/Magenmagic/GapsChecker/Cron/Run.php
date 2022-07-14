<?php

namespace Magenmagic\GapsChecker\Cron;

class Run
{
    protected $logger;
    protected $salesOrderCollectionFactory;
    protected $config;
    protected $resourceConnection;
    protected $mailer;
    protected $log;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory,
        \Magenmagic\GapsChecker\Helper\Config $config,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magenmagic\GapsChecker\Model\Mailer $mailer,
        \Magenmagic\HealthCheck\Api\LoggerInterface $log
    )
    {
        $this->logger = $logger;
        $this->salesOrderCollectionFactory = $salesOrderCollectionFactory;
        $this->config = $config;
        $this->resourceConnection = $resourceConnection;
        $this->mailer = $mailer;
        $this->log = $log;
    }

    public function lastOrder()
    {

        if (!$this->config->isEnable()) {
            return;
        }
        $connection = $this->resourceConnection->getConnection();
        $timeZone = $this->config->getTimeZone();

        if ($this->config->getEnableSalesChannels()) {
            $salesChannels = $this->config->getSalesChannelsString();
            $lastOrderCreatedAt = $connection->fetchOne("SELECT `created_at`  FROM `sales_order` where `sales_channel` in ($salesChannels) order by `created_at` desc limit 1");
        } else {
            $lastOrderCreatedAt = $connection->fetchOne("SELECT `created_at`  FROM `sales_order`  order by `created_at` desc limit 1");
        }

        $orderCollection = $this->salesOrderCollectionFactory->create();
        $orderCollection->addFieldToFilter('created_at', $lastOrderCreatedAt);

        foreach ($orderCollection as $order) {
            $increment_id = $order->getData('increment_id');
        }

        $diffTime = time() - strtotime($lastOrderCreatedAt);


        if ($diffTime >= $this->config->getPeriod() * 60 && $this->config->isEmailEnable()) {

            $timeZoneFormat = new \DateTime($lastOrderCreatedAt);
            $timeZoneFormat->setTimezone(new \DateTimeZone($timeZone));
            $output = $timeZoneFormat->format('Y-m-d H:i:s T');
            $message = "Last order " . $increment_id . " was in $output.";

            if (!$this->config->getTestValue()) {
                $loglevel = $this->config->getLog();
                try {
                    $this->log->log($loglevel, $message);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            }

            return $message;

        }
    }

    public function gapsStatic()
    {
        if (!$this->config->isEnable()) {
            return;
        }
        $connection = $this->resourceConnection->getConnection();
        $timeZone = $this->config->getTimeZone();
        $time = date('Y-m-d H:i:s', time() - (3600 * 24));

        if ($this->config->getEnableSalesChannels()) {
            $salesChannels = $this->config->getSalesChannelsString();
            $orders = $connection->fetchall("SELECT `created_at`  FROM `sales_order` where `sales_channel` in ($salesChannels) and `created_at` > '$time'");
        } else {
            $orders = $connection->fetchall("SELECT `created_at`  FROM `sales_order` where `created_at` > '$time'");
        }


        $new_message = '';
        $message = '';
        $count = 1;

        for ($i = 0; $i < count($orders) - 1; $i++) {
            $firstOrder = $orders[$i]['created_at'];
            $secondOrders = $orders[$i + 1]['created_at'];
            $period = $this->config->getPeriodByTime($secondOrders);
            if (strtotime($secondOrders) - strtotime($firstOrder) >= $period * 60) {
                $formatDateFirstOrder = new \DateTime($firstOrder);
                $formatDateFirstOrder->setTimezone(new \DateTimeZone($timeZone));
                $firstOutput = $formatDateFirstOrder->format('Y-m-d H:i:s');

                $formatDateSecondOrder = new \DateTime($secondOrders);
                $formatDateSecondOrder->setTimezone(new \DateTimeZone($timeZone));
                $secondOutput = $formatDateSecondOrder->format('Y-m-d H:i:s T');

                $message .= $count++ . ". $firstOutput - $secondOutput ";
                $new_message = preg_replace("#(\d\.\s)#", "\n\${1}", $message);

            }
        }


        if ($this->config->getEnableGapsStatic()) {

            if ($new_message !== '') {

                if (!$this->config->getTestValue()) {

                    $loglevel = $this->config->getLog();
                    try {
                        $this->log->log($loglevel, $new_message);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }

                return $new_message;
            } else {
                if (count($orders) > 0) {
                    $new_message = 'No Gaps.';
                } else {
                    $new_message = 'No Orders.';
                }

                if (!$this->config->getTestValue()) {
                    $loglevel = $this->config->getLogStatic();
                    try {
                        $this->log->log($loglevel, $new_message);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }

                return $new_message;
            }
        }
    }
}
