<?php


namespace Magenmagic\PsrLog\Model\Config;


class Options implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'DEBUG',
                'label' => 'DEBUG'
            ],
            [
                'value' => 'INFO',
                'label' => 'INFO'
            ],
            [
                'value' => 'NOTICE',
                'label' => 'NOTICE'
            ],
            [
                'value' => 'WARNING',
                'label' => 'WARNING'
            ],
            [
                'value' => 'ERROR',
                'label' => 'ERROR'
            ],
            [
                'value' => 'CRITICAL',
                'label' => 'CRITICAL'
            ],
            [
                'value' => 'ALERT',
                'label' => 'ALERT'
            ],
            [
                'value' => 'EMERGENCY',
                'label' => 'EMERGENCY'
            ]
        ];
    }
}
