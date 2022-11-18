<?php


namespace Magenmagic\SalesCheck\Model\Config;


class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    const OPTION_CLI = 'cli';
    const OPTION_CRON = 'cron';
    const OPTION_ORDER_PLACE = 'op';

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::OPTION_CLI,
                'label' => 'CLI'
            ],
            [
                'value' => self::OPTION_CRON,
                'label' => 'Cron'
            ],
            [
                'value' => self::OPTION_ORDER_PLACE,
                'label' => 'When Order Placed(Not Recomended)'
            ],
        ];
    }
}
