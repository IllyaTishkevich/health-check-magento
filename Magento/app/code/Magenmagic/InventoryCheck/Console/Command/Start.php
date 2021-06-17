<?php
/**
 *
 *  * @author MagenMagic Team
 *  * @copyright Copyright (c) 2020 MagenMagic (https://www.magenmagic.com)
 *  * @package
 *
 */

namespace Magenmagic\InventoryCheck\Console\Command;

use Magento\Deploy\Process\Queue;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * magenmagic:fixinventory_reservation:start command line
 *
 * @version 1.0.0
 * @description <pre>
 * $ bin/magento help magenmagic:fixinventory_reservation:start
 * Usage:
 * magenmagic:fixinventory_reservation:start
 *
 * Options:
 * --help (-h)           Display this help message
 * </pre>
 */
class Start extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_state = null;

    protected $helper = null;

    const INPUT_STORE_IDS = 'ids';

    const INPUT_DRY_RUN   = 'dry-run';

    /**
     * Class constructor
     *
     * @param \Magento\Cron\Model\ConfigFactory $cronConfigFactory
     * @param \Magento\Framework\App\State      $state
     */
    public function __construct(
        \Magenmagic\InventoryCheck\Helper\Data $helper, \Magento\Framework\App\State $state
    ) {
        $this->_state = $state;
        $this->helper = $helper;
        parent::__construct();
    }

    /**
     * Configure the command line
     */
    protected function configure()
    {
        $this->setName('magenmagic:fixinventory_reservation:start')
            ->setDescription(__('Fix Inventory Reservations'))
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
}