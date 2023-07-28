<?php
namespace Magenmagic\HealthCheck\Controller\Adminhtml\Module;

class Status extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }


    public function execute()
    {
        $result = $this->resultPageFactory->create();
        $result->getConfig()->getTitle()->set((__('Module Status')));

        return $result;
    }
}
