<?php
/**
 *
 *  * @author MagenMagic Team
 *  * @copyright Copyright (c) 2020 MagenMagic (https://www.magenmagic.com)
 *  * @package
 *
 */

namespace Magenmagic\InventoryCheck\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use \Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\App\Area;

class Sendmail extends \Magento\Framework\App\Helper\AbstractHelper
{
    const EMAIL_TEMPLATE                 = 'mm_cron_jobs/mmcheckinventory/email_template';

    const CONFIG_MODULE_ENABLED          = 'mm_cron_jobs/mmcheckinventory/enabled';

    const CONFIG_MODULE_SENDMAIL_ENABLED = 'mm_cron_jobs/mmcheckinventory/email_enabled';

    const CONFIG_EMAIL_SENDER            = 'mm_cron_jobs/mmcheckinventory/sender';

    const CONFIG_EMAIL_RECEIVER          = 'mm_cron_jobs/mmcheckinventory/email_to';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    protected $_dir;

    private $filesystem;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    public function __construct(
        Context $context, StoreManagerInterface $storeManager, TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation, LoggerInterface $logger, \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\DirectoryList $dir
    ) {
        $this->storeManager      = $storeManager;
        $this->transportBuilder  = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->logger            = $logger;
        $this->filesystem        = $filesystem;
        $this->_dir              = $dir;

        parent::__construct($context);
    }

    /**
     * Send Mail
     *
     * @return $this
     *
     * @throws LocalizedException
     * @throws MailException
     */

    public function sendMail($file)
    {
        if (!$this->getSendMailEnabled()) {
            return $this;
        }
        $filePath = $this->_dir->getPath('var') . '/' . $file; //set with path
        $body     = file_get_contents($filePath);
        $fileName = pathinfo($file, PATHINFO_BASENAME);

        // set receiver mail
        $email = $this->getRecieverEmail();
        $email = explode(',', $email);

        $this->inlineTranslation->suspend();
        $storeId = $this->getStoreId();

        /* email template */
        $template = $this->scopeConfig->getValue(
            self::EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $vars = [
            'message_1' => 'CUSTOM MESSAGE STR 1',
            'message_2' => 'custom message str 2',
            'store'     => $this->getStore(),
        ];

        // set from email
        $sender = $this->scopeConfig->getValue(
            self::CONFIG_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );

        $transport = $this->transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            [
                'area'  => Area::AREA_FRONTEND,
                'store' => $this->getStoreId(),
            ]
        )->setTemplateVars(
            $vars
        )->addAttachment(
            $body,
            \Zend_Mime::TYPE_OCTETSTREAM,
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
            $fileName
        )->setFromByScope(
            $sender
        )->addTo(
            $email
        )->addBcc(
            $email
        )->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }
        $this->inlineTranslation->resume();

        return $this;
    }

    private function getRecieverEmail()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_EMAIL_RECEIVER,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    private function getSendMailEnabled()
    {
        return $this->scopeConfig->getValue(
            self::CONFIG_MODULE_SENDMAIL_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    private function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /*
     * get Current store Info
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }
}
