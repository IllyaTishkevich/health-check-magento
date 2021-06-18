<?php

namespace Magenmagic\GalleryCheck\Console;

use Magenmagic\GalleryCheck\Helper\Config;
use Magenmagic\GalleryCheck\Helper\Data;
use Magenmagic\HealthCheck\Api\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckImages extends Command
{
    protected $helper;
    protected $config;
    protected $log;

    public function __construct(
        Data $helper,
        Config $config,
        LoggerInterface $log
    ) {
        $this->helper = $helper;
        $this->config = $config;
        $this->log = $log;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('magenamagic:gallery:check');
        $this->setDescription('Check hidden images');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->config->getEnabled()) {
            if ($this->config->getLog()) {
                $loglevel = $this->config->getLog();
                $message = $this->helper->getHiddenImageId();

                try {
                    $this->log->log($loglevel, $message, '172.18.0.6');
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                $output->writeln(__('Please set log'));
            }
        } else {
            $output->writeln(__('module Magenmagic_GalleryCheck is not enabled'));
        }
    }
}
