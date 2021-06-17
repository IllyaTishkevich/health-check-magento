<?php

/**
 * @author    MagenMagic Team
 * @copyright Copyright (c) 2020 MagenMagic (https://www.magenmagic.com)
 * @package Magenmagic_CatalogInventory
 */

namespace Magenmagic\CatalogInventory\Cron;


class SetToEnable {

    private $mmHelper;
    private $setModel = null;

    public function __construct(
        \Magenmagic\CatalogInventory\Helper\Data $helper,
        \Magenmagic\CatalogInventory\Model\ResourceModel\SetToEnable $setModel
    )
    {
        $this->mmHelper = $helper;
        $this->setModel = $setModel;
    }


    public function execute()
    {
        if ($this->mmHelper->getConfigValue('mmcataloginventory/settoenable/cron_enable'))
        {
            try
            {
                $configurIds = $this->setModel->execute();

                if ($configurIds && $this->mmHelper->getConfigValue('mmcataloginventory/settoenable/email_use'))
                {
                    $this->mmHelper->sendEmailToAdmin($configurIds);
                }
            }
            catch (\Exception $e)
            {
                $this->mmHelper->log(__METHOD__ . __LINE__ . ': ' . $e->getMessage());
            }
        }
    }


}
