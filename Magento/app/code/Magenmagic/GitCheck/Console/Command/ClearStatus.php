<?php

namespace Magenmagic\GitCheck\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magenmagic\GitCheck\Helper\Data as GitCheckHelper;
use Magenmagic\HealthCheck\Api\LoggerInterface;

class ClearStatus extends Command
{
    protected $gitCheckHelper;
    
    protected $logger;

    public function __construct(GitCheckHelper $gitCheckHelper, LoggerInterface $logger)
    {
        $this->gitCheckHelper = $gitCheckHelper;
        $this->logger         = $logger;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('magenmagic:git:clear')->setDescription('Clear current branch status');

        parent::configure();
    }

    /**
     * Check Git Status output
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->gitCheckHelper->getIsEnabled()) {
            return;
        }
        
        set_time_limit(0);
        
        $this->gitCheckHelper->saveStatus("");
        $this->writelog("Cleared git status");
    }
    
    protected function writeLog($message)
    {
        $result = json_encode($message);
        $this->logger->log($this->gitCheckHelper->getLogId(), $result, "91.217.13.216");
    }
}
