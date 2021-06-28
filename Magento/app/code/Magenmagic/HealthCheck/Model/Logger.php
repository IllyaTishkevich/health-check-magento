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
        \Magenmagic\HealthCheck\Helper\Logger $logger
    ) {
        $this->config = $config;
        $this->transactionFactory = $transactionFactory;
        $this->logger = $logger;
    }

    public function emergency(string $message, string $ip = null)
    {
        $this->log('EMERGENCY', $message, $ip);
    }

    public function alert(string $message, string $ip = null)
    {
        $this->log('ALERT', $message, $ip);
    }

    public function critical(string $message, string $ip = null)
    {
        $this->log('CRITICAL', $message, $ip);
    }

    public function error(string $message, string $ip = null)
    {
        $this->log('ERROR', $message, $ip);
    }

    public function warning(string $message, string $ip = null)
    {
        $this->log('WARNING', $message, $ip);
    }

    public function notice(string $message, string $ip = null)
    {
        $this->log('NOTICE', $message, $ip);
    }

    public function info(string $message, string $ip = null)
    {
        $this->log('INFO', $message, $ip);
    }

    public function debug(string $message, string $ip = null)
    {
        $this->log('DEBUG', $message, $ip);
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
            $transaction->getResponce();
        } catch (\Exception $e) {
            $this->logger->log('ERROR', $e->getMessage());
        }
    }
}
