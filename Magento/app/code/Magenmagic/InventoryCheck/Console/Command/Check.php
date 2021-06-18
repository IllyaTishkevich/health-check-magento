<?php
/**
 *
 *  * @author MagenMagic Team
 *  * @copyright Copyright (c) 2021 MagenMagic (https://www.magenmagic.com)
 *  * @package
 *
 */

namespace Magenmagic\InventoryCheck\Console\Command;

use Magenmagic\InventoryCheck\Helper\Data;
use Magento\Deploy\Process\Queue;
use Magento\Framework\App\State;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Magenmagic\HealthCheck\Api\LoggerInterface;

class Check extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var State
     */
    protected $_state = null;

    protected $helper = null;

    protected $logger = null;

    const INPUT_STORE_IDS = 'ids';

    const INPUT_DRY_RUN   = 'dry-run';

    /**
     * Class constructor
     *
     * @param \Magento\Cron\Model\ConfigFactory $cronConfigFactory
     * @param State      $state
     */
    public function __construct(
        \Magenmagic\InventoryCheck\Helper\Data $helper, \Magento\Framework\App\State $state, LoggerInterface $logger
    ) {
        $this->_state = $state;
        $this->helper = $helper;
        $this->logger = $logger;
        parent::__construct();
    }

    /**
     * Configure the command line
     */
    protected function configure()
    {
        $this->setName('magenamagic:inventory:check')
            ->setDescription(__('Send Inventory Reservations Log'))
            ->setDefinition($this->getOptions());
        parent::configure();
    }

    /**
     * Execute the command line
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int \Magento\Framework\Console\Cli::RETURN_FAILURE or \Magento\Framework\Console\Cli::RETURN_SUCCESS
     */
    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        try {
            $skus = [];
            $this->_state->setAreaCode('adminhtml');
            if ($this->helper->isEnabled()) {
                $storeIds = explode(',', $input->getOption(self::INPUT_STORE_IDS));
                if (sizeof($storeIds) == 0) {
                    $storeIds[] = 0;
                }
                $dryRun = (bool)$input->getOption(self::INPUT_DRY_RUN);
                foreach ($storeIds as $storeId) {

                    $skus = $this->helper->fixInventory($storeId, $dryRun);
                }
            }
            if (sizeof($skus) > 0) {
                $output->writeln(__('Processed skus #  %1', implode(',', array_unique($skus))));
            }
            $output->writeln('<info>Success Message.</info>');
            $this->logger->log($this->helper->logLvl(), $skus);
            $returnValue = \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $output->writeln($e->getMessage());
            $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return $returnValue;
    }

    protected function getOptions()
    {
        return [
            new InputOption(
                self::INPUT_STORE_IDS,
                null,
                InputOption::VALUE_NONE | InputOption::VALUE_REQUIRED,
                'Comma separated Store Ids. (i.e. 0,1,2) 0 - default store view'
            ),
            new InputOption(
                self::INPUT_DRY_RUN, null, InputOption::VALUE_NONE | InputOption::VALUE_REQUIRED, '--dry-run true'
            ),

        ];
    }

    protected function writeLog($message)
    {
        $result = json_encode($message);
        $this->logger->log($this->gitCheckHelper->getLogId(), $result, "");
    }
}
