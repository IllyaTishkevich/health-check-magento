<?php


namespace Magenmagic\PlaceOrderChecker\Model;

use Magenmagic\PlaceOrderChecker\Helper\Data as Helper;

class SendToHealthCheckLogger
{
    /**
     * @var \Magenmagic\HealthCheck\Api\LoggerInterface
     */
    private $loggerHealthCheck;
    /**
     * @var Helper
     */
    private $helper;
    /**
     * @var ResourceModel\Checker
     */
    private $checkerResourceModel;

    /**
     * SendToHealthCheckLogger constructor.
     * @param \Magenmagic\HealthCheck\Api\LoggerInterface $loggerHealthCheck
     * @param Helper $helper
     * @param ResourceModel\Checker $checkerResourceModel
     */
    public function __construct(
        \Magenmagic\HealthCheck\Api\LoggerInterface $loggerHealthCheck,
        Helper $helper,
        ResourceModel\Checker $checkerResourceModel
    )
    {
        $this->loggerHealthCheck = $loggerHealthCheck;
        $this->helper = $helper;
        $this->checkerResourceModel = $checkerResourceModel;
    }

    public function send($data)
    {
        $logLevel = $this->helper->getLogLevel();
        $this->loggerHealthCheck->log($logLevel, json_encode($data));
        $this->checkerResourceModel->isSendedFlag($data['entity_id']);
    }
}
