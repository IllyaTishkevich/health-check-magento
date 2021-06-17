<?php

namespace Magenmagic\PlaceOrderChecker\Cron;


class Check {

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
     * Check constructor.
     * @param \Magenmagic\PlaceOrderChecker\Helper\Data $helper
     * @param \Magenmagic\PlaceOrderChecker\Model\ResourceModel\Checker $checkerResourceModel
     * @param \Magenmagic\PlaceOrderChecker\Model\SendToHealthCheckLogger $sendToHealthCheckLogger
     */
    public function __construct(
        \Magenmagic\PlaceOrderChecker\Helper\Data $helper,
        \Magenmagic\PlaceOrderChecker\Model\ResourceModel\Checker $checkerResourceModel,
        \Magenmagic\PlaceOrderChecker\Model\SendToHealthCheckLogger $sendToHealthCheckLogger,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->helper = $helper;
        $this->checkerResourceModel = $checkerResourceModel;
        $this->sendToHealthCheckLogger = $sendToHealthCheckLogger;
        $this->logger = $logger;
    }


    public function execute()
    {
        if ($this->helper->isCronEnabled())
        {
            try
            {
                $entities = $this->checkerResourceModel->getList();

                if ($entities) {
                    foreach ($entities as $entity) {
                        $this->sendToHealthCheckLogger->send($entity);
                    }
                }
                $this->helper->setLastCronCheck(date('Y-m-d H:i:s'));
            }
            catch (\Exception $e)
            {
                $this->logger->critical('HealthCheck PlaceOrder cron error', ['exception' => $e]);
            }
        }
    }


}
