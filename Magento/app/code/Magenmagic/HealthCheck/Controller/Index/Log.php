<?php


namespace Magenmagic\HealthCheck\Controller\Index;


use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;

class Log extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{

    protected $config;

    protected $logger;

    protected $url;

    protected $resultRedirectFactory;

    protected $resultFactory;

    public function __construct(
        Context $context,
        \Magenmagic\HealthCheck\Helper\Config $config,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->url = $url;

        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }

    public function execute()
    {
        $result = null;

        if ($this->getRequest()->isAjax() && $this->config->isEnable()) {
            $json = json_encode($this->getRequest()->getParams());
            $this->logger->debug('HEADER:'.$this->getRequest()->getHeader('Authentication-Key').', BODY:'.$json);
            $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON);
        } else {
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $norouteUrl = $this->url->getUrl('noroute');
            $result = $resultRedirect->setUrl($norouteUrl);
        }
        return $result;
    }
}
