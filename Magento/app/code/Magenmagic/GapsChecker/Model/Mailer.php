<?php


namespace Magenmagic\GapsChecker\Model;

use Magento\Framework\App\Area;

class Mailer
{
    protected $config;
    protected $transportBuilderFactory;
    protected $inlineTranslation;
    protected $dir;
    protected $storeManager;
    protected $logger;

    public function __construct(
        \Magenmagic\GapsChecker\Helper\Config $config,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Mail\Template\TransportBuilderFactory $transportBuilderFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->config = $config;
        $this->transportBuilderFactory = $transportBuilderFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->dir = $dir;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function send($mesage, $method)
    {
        $transportBuilder = $this->transportBuilderFactory->create();

        // set receiver mail
        $email = $this->config->getEmailRecipient();
        $email = explode(',', $email);

        $this->inlineTranslation->suspend();

        /* email template */

        if ($method == "gapStatic") {
            $template = $this->config->getEmailTemplateStatic();
        } else {
            $template = $this->config->getEmailTemplate();
        }


        $tz = 'America/Los_Angeles';
        $date = new \DateTime( 'NOW', new \DateTimeZone($tz));
        $formatDate = $date->format('Y-m-d H:i:s T');

        $vars = [
            'message' => $mesage,
            'date' => $formatDate
        ];

        // set from email
        $sender = $this->config->getEmailSender();

        $transport = $transportBuilder->setTemplateIdentifier(
            $template
        )->setTemplateOptions(
            [
                'area' => Area::AREA_FRONTEND,
                'store' => $this->getStoreId()
            ]
        )->setTemplateVars(
            $vars
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

    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}