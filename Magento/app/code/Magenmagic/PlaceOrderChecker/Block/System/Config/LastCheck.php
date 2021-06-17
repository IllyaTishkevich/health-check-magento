<?php


namespace Magenmagic\PlaceOrderChecker\Block\System\Config;


use Magento\Framework\Data\Form\Element\AbstractElement;

class LastCheck extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_helper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magenmagic\PlaceOrderChecker\Helper\Data $helper
    )
    {
        $this->_helper = $helper;
        parent::__construct($context);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $getLastCheck = $this->_helper->getLastCheck();
        $element->setValue($getLastCheck);
        return $element->getValue();
    }
}
