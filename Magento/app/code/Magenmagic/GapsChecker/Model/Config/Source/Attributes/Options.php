<?php


namespace Magenmagic\GapsChecker\Model\Config\Source\Attributes;


class Options implements \Magento\Framework\Option\ArrayInterface
{

    public function toOptionArray()
    {
       return [
           [
               'value' => 'Web',
               'label' => 'Web'
           ],
           [
               'value' => 'Phone',
               'label' => 'Phone'
           ],
           [
               'value' => 'Amazon',
               'label' => 'Amazon'
           ],
           [
               'value' => 'Ebay',
               'label' => 'Ebay'
           ]
       ];
    }
}