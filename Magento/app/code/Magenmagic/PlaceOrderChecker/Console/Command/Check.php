<?php

namespace Magenmagic\PlaceOrderChecker\Console\Command;

use Psr\Log\LoggerInterface as Logger;
/**
 * Class Check
 * @package Magenmagic\PlaceOrderChecker\Console\Command
 */
class Check extends \Symfony\Component\Console\Command\Command {

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;
    /**
     * @var \Magenmagic\PlaceOrderChecker\Helper\Data
     */
    private $helper;
    /**
     * @var \Magenmagic\PlaceOrderChecker\Model\ResourceModel\Checker
     */
    private $checkerResourceModel;
    /**
     * @var \Magenmagic\PlaceOrderChecker\Model\SendToHealthCheckLogger
     */
    private $sendToHealthCheckLogger;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Check constructor.
     * @param \Magenmagic\PlaceOrderChecker\Helper\Data $helper
     * @param \Magenmagic\PlaceOrderChecker\Model\ResourceModel\Checker $checkerResourceModel
     * @param \Magenmagic\PlaceOrderChecker\Model\SendToHealthCheckLogger $sendToHealthCheckLogger
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magenmagic\PlaceOrderChecker\Helper\Data $helper,
        \Magenmagic\PlaceOrderChecker\Model\ResourceModel\Checker $checkerResourceModel,
        \Magenmagic\PlaceOrderChecker\Model\SendToHealthCheckLogger $sendToHealthCheckLogger,
        \Magento\Framework\App\State $state,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->state    = $state;
        $this->helper = $helper;
        $this->checkerResourceModel = $checkerResourceModel;
        $this->sendToHealthCheckLogger = $sendToHealthCheckLogger;
        $this->logger = $logger;

        parent::__construct();
    }


    /**
     * Configure the command line
     */
    protected function configure()
    {
        $this->setName('magenmagic:placeorder:check')
            ->setDescription(__('Send to Magenmagic_HealthCkeck\Api\LogInterface log info'))
            ->setDefinition([]);
        parent::configure();
    }


    /**
     * Execute the command line
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int \Magento\Framework\Console\Cli::RETURN_FAILURE or \Magento\Framework\Console\Cli::RETURN_SUCCESS
     */
    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    )
    {
        try
        {
            $this->state->setAreaCode('adminhtml');
            $entities = $this->checkerResourceModel->getList();

            if ($entities && $this->helper->isEnabled())
            {
                foreach ($entities as $entity) {
                    $this->sendToHealthCheckLogger->send($entity);
                }
            }
            $this->helper->setLastCheck(date('Y-m-d H:i:s'));
            $returnValue = \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        }
        catch (\Exception $e)
        {
            $output->writeln($e->getMessage());
            $this->logger->critical('HealthCheck PlaceOrder cli error', ['exception' => $e]);
            $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return $returnValue;
    }


}
