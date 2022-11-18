<?php

namespace Magenmagic\SalesCheck\Console\Command\Sales;

use Magenmagic\SalesCheck\Helper\Data;

class Check extends \Symfony\Component\Console\Command\Command {

    /**
     * @var \Magenmagic\SalesCheck\Model\Sales\Order\Data
     */
    private $_orderChk = null;

    /**
     * @var \Magenmagic\HealthCheck\Api\LoggerInterface
     */
    private $_logger   = null;

    /**
     * @var Data
     */
    private $_helper   = null;

    /**
     * Check constructor.
     * @param \Magenmagic\SalesCheck\Model\Sales\Order\Data $order
     * @param \Magenmagic\HealthCheck\Api\LoggerInterface $logger
     * @param Data $helper
     * @param string|null $name
     */
    public function __construct(
        \Magenmagic\SalesCheck\Model\Sales\Order\Data $order,
        \Magenmagic\HealthCheck\Api\LoggerInterface $logger,
        Data $helper,
        string $name = null
    ) {
        parent::__construct('magenmagic:sales:check');

        $this->_orderChk = $order;
        $this->_logger   = $logger;
        $this->_helper   = $helper;
    }


    protected function configure()
    {
        $this->setName('magenmagic:sales:check');
        $this->setDescription('Send last Orders Data');

        parent::configure();
    }


    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        if ($this->_helper->getStoreConfig(Data::GENERAL_ENABLED) &&
            $this->_helper->getStoreConfig(Data::SALES_ENABLED))
        {
            $data = $this->_orderChk->getSalesData();

            if ($data) {
                $this->_logger->log(
                    $this->_helper->getStoreConfig(
                        Data::LOG_LEVEL),
                    json_encode($data));
            }
        }

        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
