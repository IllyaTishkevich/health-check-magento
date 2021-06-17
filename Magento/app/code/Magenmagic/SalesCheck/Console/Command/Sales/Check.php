<?php

namespace Magenmagic\SalesCheck\Console\Command\Sales;


class Check extends \Symfony\Component\Console\Command\Command {

    private $_orderChk = null;
    private $_logger   = null;
    private $_helper   = null;

    public function __construct(
        \Magenmagic\SalesCheck\Model\Sales\Order\Count $order,
        \Magenmagic\SalesCheck\Helper\Data $helper,
        \Magenmagic\HealthCheck\Api\LoggerInterface $logger,
        string $name = null
    )
    {
        parent::__construct('magenmagic:sales:check');

        $this->_orderChk = $order;
        $this->_logger   = $logger;
        $this->_helper   = $helper;
    }


    protected function configure()
    {
        $this->setName('magenmagic:sales:check');
        $this->setDescription('Check healthy of Ordering');

        parent::configure();
    }


    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output)
    {
        if ($this->_helper->getStoreConfig('mm_health_check/general/enabled') &&
            $this->_helper->getStoreConfig('mm_health_check/sales/enabled'))
        {
            $count = $this->_orderChk->getForLastHour();

            $this->_logger->log(
                $this->_helper->getStoreConfig(
                    'mm_health_check/sales/log_level'),
                    json_encode(['last_hour_order_count' => $count]));
        }

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }


}
