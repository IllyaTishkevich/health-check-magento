<?php

/**
 * @author    MagenMagic Team
 * @copyright Copyright (c) 2020 MagenMagic (https://www.magenmagic.com)
 * @package Magenmagic_CatalogInventory
 */

namespace Magenmagic\CatalogInventory\Console\Command;


/**
 * magenmagic:cataloginventory:settoenable command line
 * @version 1.0.0
 * @description <pre>
 * $ bin/magento help magenmagic:cataloginventory:settoenable
 * Usage:
 * magenmagic:cataloginventory:settoenable
 *
 * Options:
 * --help (-h)           Display this help message
 * </pre>
 */
class SetToEnable extends \Symfony\Component\Console\Command\Command {

    /**
     * @var \Magento\Framework\App\State
     */
    private $state    = null;
    private $mmHelper = null;
    private $setModel = null;

    /**
     * Class constructor
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magenmagic\CatalogInventory\Helper\Data $helper,
        \Magenmagic\CatalogInventory\Model\ResourceModel\SetToEnable $setModel,
        \Magento\Framework\App\State $state
    )
    {
        $this->state    = $state;
        $this->mmHelper = $helper;
        $this->setModel = $setModel;

        parent::__construct();
    }


    /**
     * Configure the command line
     */
    protected function configure()
    {
        $this->setName('magenmagic:cataloginventory:settoenable')
            ->setDescription(__('Setting Status of configurable product to Enable if it has in-stocked child product.'))
            ->setDefinition([]);
        parent::configure();
    }


    /**
     * Execute the command line
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int \Magento\Framework\Console\Cli::RETURN_FAILURE or \Magento\Framework\Console\Cli::RETURN_SUCCESS
     */
    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    )
    {
        try
        {
            $this->state->setAreaCode('adminhtml');

            $configurIds = $this->setModel->execute();

            if ($configurIds && $this->mmHelper->getConfigValue('mmcataloginventory/settoenable/email_use'))
            {
                $this->mmHelper->sendEmailToAdmin($configurIds);
            }

            $returnValue = \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        }
        catch (\Exception $e)
        {
            $output->writeln($e->getMessage());
            $this->mmHelper->log(__METHOD__ . __LINE__ . ': ' . $e->getMessage());
            $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return $returnValue;
    }


}
