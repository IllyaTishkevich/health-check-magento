<?php

namespace Magenmagic\GitCheck\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magenmagic\GitCheck\Helper\Data as GitCheckHelper;
use Magenmagic\HealthCheck\Api\LoggerInterface;

class Check extends Command
{
    const RESPONSE = 'response';

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
        $this->setName('magenmagic:git:check')->setDescription('Check current branch status');
        $this->addOption(
                    self::RESPONSE,
                    '-r',
                    InputOption::VALUE_NONE,
                    'Git Check Status'
                );
        
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
        
        $oldResult = $this->gitCheckHelper->getStatus();
        $resArray = [];

        if (!$status = $input->getOption(self::RESPONSE)) {
            $status = shell_exec('git status');
        }
        
        if (is_null($status) || $status == 1) {
            $output->writeln("<error>Not a Git Repository</error>");
            $this->writeLog("Not a Git Repository");
        } else { 
            $branchName = explode("\n", $status)[0];
            $resArray[] = $branchName;
            
            $changesArray = explode("git add/rm", $status);
            if (count($changesArray) > 1) {
                $resArray[] = "There are changes in repository";
            }
            $newArray = explode("git add ", $status);
            if (count($newArray) > 1) {
                $resArray[] = "There are new files in repository";
            }
            if (strlen($oldResult) != strlen($status)) {
                $this->gitCheckHelper->saveStatus($status);
                $resArray[] = "New changes after last check";
            }
            foreach ($resArray as $item) {
                $output->writeln("<info>$item</info>");
            }
            $resArray[] = $status;
            $this->writelog($resArray);
        }
    }
    
    protected function writeLog($message)
    {
        $result = json_encode($message);
        $this->logger->log($this->gitCheckHelper->getLogId(), $result, "");
    }
}
