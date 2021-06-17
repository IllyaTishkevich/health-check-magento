<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenmagic\PlaceOrderChecker\Model;

use Magento\Checkout\Api\Exception\PaymentProcessingRateLimitExceededException;
use Magento\Checkout\Api\PaymentProcessingRateLimiterInterface;
use Magento\Checkout\Api\PaymentSavingRateLimiterInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Model\Quote;

/**
 * Guest payment information management model.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GuestPaymentInformationManagement implements \Magento\Checkout\Api\GuestPaymentInformationManagementInterface
{

    /**
     * @var \Magento\Quote\Api\GuestBillingAddressManagementInterface
     */
    protected $billingAddressManagement;

    /**
     * @var \Magento\Quote\Api\GuestPaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var \Magento\Quote\Api\GuestCartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var \Magento\Checkout\Api\PaymentInformationManagementInterface
     */
    protected $paymentInformationManagement;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var PaymentProcessingRateLimiterInterface
     */
    private $paymentsRateLimiter;

    /**
     * @var PaymentSavingRateLimiterInterface
     */
    private $savingRateLimiter;

    /**
     * @var bool
     */
    private $saveRateLimitDisabled = false;

    private $resource;

    /**
     * @var \Magenmagic\PlaceOrderChecker\Helper\Data
     */
    private $helper;
    /**
     * @var \Magenmagic\PlaceOrderChecker\Model\ResourceModel\Checker
     */
    private $checkerResourceModel;
    /**
     * @var \Magenmagic\PlaceOrderChecker\Model\SendToHealthCheckLogger
     */
    private $sendToHealthCheckLogger;

    /**
     * @param \Magento\Quote\Api\GuestBillingAddressManagementInterface $billingAddressManagement
     * @param \Magento\Quote\Api\GuestPaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Quote\Api\GuestCartManagementInterface $cartManagement
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @param PaymentProcessingRateLimiterInterface|null $paymentsRateLimiter
     * @param PaymentSavingRateLimiterInterface|null $savingRateLimiter
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Quote\Api\GuestBillingAddressManagementInterface $billingAddressManagement,
        \Magento\Quote\Api\GuestPaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Api\GuestCartManagementInterface $cartManagement,
        \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        CartRepositoryInterface $cartRepository,
        ?PaymentProcessingRateLimiterInterface $paymentsRateLimiter = null,
        ?PaymentSavingRateLimiterInterface $savingRateLimiter = null,
        \Magenmagic\PlaceOrderChecker\Helper\Data $helper,
        \Magenmagic\PlaceOrderChecker\Model\ResourceModel\Checker $checkerResourceModel,
        \Magenmagic\PlaceOrderChecker\Model\SendToHealthCheckLogger $sendToHealthCheckLogger
    ) {
        $this->billingAddressManagement = $billingAddressManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartManagement = $cartManagement;
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->resource = $resource;
        $this->cartRepository = $cartRepository;
        $this->paymentsRateLimiter = $paymentsRateLimiter
            ?? ObjectManager::getInstance()->get(PaymentProcessingRateLimiterInterface::class);
        $this->savingRateLimiter = $savingRateLimiter
            ?? ObjectManager::getInstance()->get(PaymentSavingRateLimiterInterface::class);
        $this->helper = $helper;
        $this->checkerResourceModel = $checkerResourceModel;
        $this->sendToHealthCheckLogger = $sendToHealthCheckLogger;
    }

    /**
     * @inheritdoc
     */
    public function savePaymentInformationAndPlaceOrder(
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $this->createLogRow($cartId, $email);
        $this->paymentsRateLimiter->limit();
        try {
            //Have to do this hack because of savePaymentInformation() plugins.
            $this->saveRateLimitDisabled = true;
            $this->savePaymentInformation($cartId, $email, $paymentMethod, $billingAddress);
        } finally {
            $this->saveRateLimitDisabled = false;
        }
        try {
            $orderId = $this->cartManagement->placeOrder($cartId);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->getLogger()->critical(
                'Placing an order with quote_id ' . $cartId . ' is failed: ' . $e->getMessage()
            );
            $this->logException($cartId, $email, $e->getMessage());
            throw new CouldNotSaveException(
                __($e->getMessage()),
                $e
            );
        } catch (\Exception $e) {
            $this->getLogger()->critical($e);
            $this->logException($cartId, $email, $e->getMessage());
            throw new CouldNotSaveException(
                __('An error occurred on the server. Please try to place the order again.'),
                $e
            );
        }

        return $orderId;
    }

    /**
     * @inheritdoc
     */
    public function savePaymentInformation(
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        if (!$this->saveRateLimitDisabled) {
            try {
                $this->savingRateLimiter->limit();
            } catch (PaymentProcessingRateLimitExceededException $ex) {
                //Limit reached
                return false;
            }
        }

        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($quoteIdMask->getQuoteId());

        if ($billingAddress) {
            $billingAddress->setEmail($email);
            $quote->removeAddress($quote->getBillingAddress()->getId());
            $quote->setBillingAddress($billingAddress);
            $quote->setDataChanges(true);
        } else {
            $quote->getBillingAddress()->setEmail($email);
        }
        $this->limitShippingCarrier($quote);

        $this->paymentMethodManagement->set($cartId, $paymentMethod);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getPaymentInformation($cartId)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->paymentInformationManagement->getPaymentInformation($quoteIdMask->getQuoteId());
    }

    /**
     * Get logger instance
     *
     * @return \Psr\Log\LoggerInterface
     * @deprecated 100.1.8
     */
    private function getLogger()
    {
        if (!$this->logger) {
            $this->logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
        }
        return $this->logger;
    }

    /**
     * Limits shipping rates request by carrier from shipping address.
     *
     * @param Quote $quote
     *
     * @return void
     * @see \Magento\Shipping\Model\Shipping::collectRates
     */
    private function limitShippingCarrier(Quote $quote) : void
    {
        $shippingAddress = $quote->getShippingAddress();
        if ($shippingAddress && $shippingAddress->getShippingMethod()) {
            $shippingRate = $shippingAddress->getShippingRateByCode($shippingAddress->getShippingMethod());
            if ($shippingRate) {
                $shippingAddress->setLimitCarrier($shippingRate->getCarrier());
            }
        }
    }

    protected function createLogRow($cartId, $email)
    {
        try {
            $customerId = $_SESSION['customer_base']['customer_id'] ?? $email;
            $ip = $_SERVER['REMOTE_ADDR'] ?? $_SESSION['checkout']['mm_customer_email'] ?? '';
            $quoteId = $_SESSION['checkout']['quote_id_1'] ?? $cartId;
            $connection = $this->resource->getConnection('core_write');
            $query = "INSERT INTO `mm_po_checker` (`quote_id`, `ip`, `customer_id`) VALUES ('$quoteId', '$ip', '$customerId');";
            $connection->exec($query);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    protected function logException($cartId, $email, $e)
    {
        try {
            $connection = $this->resource->getConnection('core_write');
            $customerId = $_SESSION['customer_base']['customer_id'] ?? $email;
            $quoteId = $_SESSION['checkout']['quote_id_1'] ?? $cartId;
            $query = "UPDATE `mm_po_checker` SET `exception_log`='{$e}'
    WHERE `quote_id`='{$quoteId}' and `customer_id`='{$customerId}' AND sended_to_healthcheck = 0";
            $connection->exec($query);
            if($this->helper->isEnabled() && $this->helper->isCheckImmediately()) {
                $entities = $this->checkerResourceModel->getList();
                if ($entities)
                {
                    foreach ($entities as $entity) {
                        $this->sendToHealthCheckLogger->send($entity);
                    }
                    $this->helper->setLastCheck(date('Y-m-d H:i:s'));
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
