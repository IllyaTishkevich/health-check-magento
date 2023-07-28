<?php
namespace Magenmagic\HealthCheck\Controller\Adminhtml\Statistics;

class Index extends \Magento\Backend\App\Action
{
    const COOKIE_NAME = 'magenmagic-hc-token';
    const COOKIE_DURATION = 86400;

    /**
     * @var \Magenmagic\HealthCheck\Model\Auth
     */
    protected $auth;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magenmagic\HealthCheck\Model\Auth $auth,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->auth = $auth;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function execute()
    {
        $this->setToken();
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('HealthCheck'));
        $this->_view->renderLayout();
    }

    /**
     * set healthcheck token in cookie
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function setToken()
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(self::COOKIE_DURATION);
        $this->cookieManager->setPublicCookie(
            self::COOKIE_NAME,
            $this->auth->getToken(),
            $metadata
        );
    }
}
