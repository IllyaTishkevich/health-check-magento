<?php

namespace Magenmagic\GapsChecker\Console;

use Magenmagic\GapsChecker\Helper\Config;
use Magenmagic\GapsChecker\Cron\Run;
use Magenmagic\HealthCheck\Api\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckGapsStaticOrder extends Command
{
    protected $run;
    protected $config;
    protected $log;

    public function __construct(
        Run $run,
        Config $config,
        LoggerInterface $log
    ) {
        $this->run = $run;
        $this->config = $config;
        $this->log = $log;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('magenamagic:gaps:static');
        $this->setDescription('Check gaps static');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->config->isEnable()) {
            if ($this->config->getLog()) {
                $loglevel = $this->config->getLogStatic();
                try {
                    if($this->config->getTestValue()){
                        $message = $this->run->gapsStatic();
                        $this->log->log($loglevel, $message );
                    } else {
                        $message = 'Please enable Test mode';
                    }

                    $output->writeln(__($message));
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                $output->writeln(__('Please set log'));
            }
        } else {
            $output->writeln(__('module Magenmagic_GapsChecker is not enabled'));
        }
    }
}
