<?php

namespace Magenmagic\UrlCheck\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use \Magento\Framework\Console\Cli;

class Check extends Command
{
    const NAME = 'option';
    
    private $toLogger;

    private $config;

    private $logger;

    private $helper;

    public function __construct(
        \Magenmagic\UrlCheck\Model\SendToLogger $toLogger,
        \Magenmagic\UrlCheck\Helper\Config $config,
        \Magenmagic\UrlCheck\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->toLogger = $toLogger;
        $this->config = $config;
        $this->helper = $helper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('magenamagic:url:check');
        $this->setDescription('Get empty product url key or url path');
        $this->addOption(
            self::NAME,
            '-o',
            InputOption::VALUE_REQUIRED,
            'option'
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $defaultStoreId  = $this->config->getDefaultStoreId();
            $option          = $input->getOption(self::NAME);
            $cliValue        = Cli::RETURN_SUCCESS;

            if(!$this->config->isEnable())
                return $cliValue;


            if(!$option){
                $result = $this->helper->checkExist('url_key', $defaultStoreId, $output);
            }

            switch ($option) {
                case 'path':
                    $result = $this->helper->checkExist('url_path', $defaultStoreId, $output);
                    break;
                case 'rewrite':
                    $result = $this->helper->checkRewrite($defaultStoreId, $output);
                    break;
                case 'produnrewrite':
                    $result = $this->helper->getProductUnRewrite($output);
                    break;
                case 'delrewrites':
                    $result = $this->helper->getProductDelRewrite($output);
                    break;
            }

            if(empty($result)){
                $output->writeln("No warnings found!");
                return $cliValue;
            }
        
            $this->toLogger->send($result);
            $output->writeln("\nSend to HealthCheck successfully!");
        } catch (\Exception $e) {
            $this->logger->critical('Magenmagic UrlCheck error', ['exception' => $e]);
            $output->writeln($e->getMessage());
            $cliValue = Cli::RETURN_FAILURE;
        }

        return $cliValue;
    }

}
