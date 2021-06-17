<?php

namespace Magenmagic\InventoryCheck\Block\System\Config;

use Magento\Backend\Block\Widget\Button;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class DryRunButton extends Field
{
    protected $_template = 'Magenmagic_InventoryCheck::system/config/dryrunbtn.phtml';

    public function getResultUrl(): string
    {
        return $this->getUrl('mmcheckinventory/dryrun');
    }

    public function getButtonHtml(): string
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $button = $this->getLayout()->createBlock(
            Button::class
        )->setData(
            [
                'class' => 'primary',
                'id'    => 'make_dry_run',
                'label' => __('Make Dry Run'),
            ]
        );

        return $button->toHtml();
    }

    protected function _getElementHtml(AbstractElement $element): string
    {
        return $this->_toHtml();
    }
}
