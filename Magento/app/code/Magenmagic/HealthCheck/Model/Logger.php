<?php


namespace Magenmagic\HealthCheck\Model;


class Logger implements \Magenmagic\HealthCheck\Api\LoggerInterface
{
    protected $config;

    protected $transactionFactory;

    protected $logger;

    public function __construct(
        \Magenmagic\HealthCheck\Helper\Config $config,
        \Magenmagic\HealthCheck\Model\TransactionFactory $transactionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->transactionFactory = $transactionFactory;
        $this->logger = $logger;
    }

    public function emergency(string $message, string $ip = null)
    {
        $this->log('emergency', $message, $ip);
    }

    public function alert(string $message, string $ip = null)
    {
        $this->log('alert', $message, $ip);
    }

    public function critical(string $message, string $ip = null)
    {
        $this->log('critical', $message, $ip);
    }

    public function error(string $message, string $ip = null)
    {
        $this->log('error', $message, $ip);
    }

    public function warning(string $message, string $ip = null)
    {
        $this->log('warning', $message, $ip);
    }

    public function notice(string $message, string $ip = null)
    {
        $this->log('notice', $message, $ip);
    }

    public function info(string $message, string $ip = null)
    {
        $this->log('info', $message, $ip);
    }

    public function debug(string $message, string $ip = null)
    {
        // TODO: Implement debug() method.
    }

    public function customError(string $message, string $ip = null)
    {
        $this->log('error', $message, $ip);
    }

    public function log(string $level, string $message, string $ip = null)
    {
        /**
         * @var $transaction \Magenmagic\HealthCheck\Model\Transaction
         */
        $transaction = $this->transactionFactory->create();
        $transaction->setLevel($level);
        $transaction->setIp($ip);
        $transaction->setBody($message);

        try {
            $response = $transaction->getResponce();
        } catch (\Exception $e) {
            $this->logger->log('error', $e->getMessage());
        }
    }
}
