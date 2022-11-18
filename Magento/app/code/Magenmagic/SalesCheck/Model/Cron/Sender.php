<?php

namespace Magenmagic\SalesCheck\Model\Cron;

use Magenmagic\SalesCheck\Helper\Data;
use Magenmagic\SalesCheck\Model\Config\Options;

class Sender
{
    /**
     * @var Data
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
     * Sender constructor.
     * @param Data $config
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

    public function execute()
    {
        if ($this->config->getStoreConfig(Data::GENERAL_ENABLED) &&
            $this->config->getStoreConfig(Data::SALES_ENABLED) &&
            ($this->config->getStoreConfig(Data::SEND_RULE) === Options::OPTION_CRON)
        ) {
            $data = $this->order->getSalesData();

            try {
                if ($data) {
                    $this->logger->log(
                        $this->config->getStoreConfig(
                            Data::LOG_LEVEL),
                        json_encode($data));
                }

                $this->hcLog->info('job healthcheck_sales_check was compile');
            } catch (\Exception $e) {
                $this->hcLog->error($e->getMessage());
            }
        }
    }
}
