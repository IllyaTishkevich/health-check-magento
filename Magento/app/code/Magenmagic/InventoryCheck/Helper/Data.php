<?php
/**
 *
 *  * @author MagenMagic Team
 *  * @copyright Copyright (c) 2020 MagenMagic (https://www.magenmagic.com)
 *  * @package
 *
 */

namespace Magenmagic\InventoryCheck\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $resourceConnection;

    protected $productRepository;

    private $resource;

    private $json;

    private $productAction;

    protected $sendmailHelper;

    protected $csvFileHelper;

    protected $txtFileHelper;

    const CONFIG_MODULE_ENABLED = 'mm_cron_jobs/mmcheckinventory/enabled';

    const AUTO_ENABLE_PRODUCTS  = 'mm_cron_jobs/mmcheckinventory/auto_enable_products';

    public function __construct(
        Context $context, StoreManagerInterface $storeManager, LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, ResourceConnection $resource,
        \Magento\Framework\Serialize\Serializer\Json $json, $productAction = null, Sendmail $sendmailHelper,
        CsvFile $csvFileHelper, TxtFile $txtFileHelper
    ) {
        $this->storeManager       = $storeManager;
        $this->logger             = $logger;
        $this->resourceConnection = $resourceConnection;
        $this->productRepository  = $productRepository;
        $this->resource           = $resource;
        $this->json               = $json;
        $this->productAction      =
            $productAction ?: ObjectManager::getInstance()->get(\Magento\Catalog\Model\Product\Action::class);

        $this->sendmailHelper = $sendmailHelper;
        $this->csvFileHelper  = $csvFileHelper;
        $this->txtFileHelper  = $txtFileHelper;
        parent::__construct($context);
    }

    /*
     * get Current store id
     */
    public function getStoreId()
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

    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_MODULE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isAutoEnableProducts()
    {
        return $this->scopeConfig->isSetFlag(
            self::AUTO_ENABLE_PRODUCTS,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function fixInventory($storeId, $dryRun = false, $ordersIds = [])
    {
        $skus                = [];
        $productIdsToDisable = [];
        $productIdsToEnable  = [];
        $reservations        = $this->getInventoryReservations();
        foreach ($reservations as $key => $reservation) {
            $metadata = $this->json->unserialize($reservation['metadata']);
            if ($metadata['event_type'] === 'order_placed' || $metadata['event_type'] === 'manual_compensation') {
                if (count($ordersIds) > 0) {
                    if (!in_array($metadata['object_increment_id'], $ordersIds)) {
                        continue;
                    }
                }
                $inventorySourceItemQty = $this->getInventorySourceItemsSum($reservation['sku']);
                $reservationSum         = $this->getInventoryReservationsSum($reservation['sku']);

                if ($reservationSum + $inventorySourceItemQty <= 0) {
                    try {
                        $product = $this->productRepository->get($reservation['sku'], true, $storeId, true);
                    } catch (\Exception $e) {
                        $this->logger->error(
                            __(
                                __METHOD__ . ' The product "%1" that was requested doesn\'t exist.',
                                $reservation['sku']
                            )
                        );
                        continue;
                    }
                    $productId              = $product->getId();
                    $quantityAndStockStatus = $product->getQuantityAndStockStatus();
                    $qty                    =
                        isset($quantityAndStockStatus['qty']) ? $quantityAndStockStatus['qty'] : 0;
                    if (!$dryRun) {
                        if ($product->getStatus() == Status::STATUS_ENABLED && $qty != 0) {
                            $productIds[$key]          = $productId;
                            $productIdsToDisable[$key] = $productId;
                        }
                    } else {
                        if ($product->getStatus() == Status::STATUS_ENABLED && $qty != 0) {
                            $skus[$key] = $reservation['sku'];
                        }
                    }
                } else {
                    if ($this->isAutoEnableProducts()) {
                        $product = $this->productRepository->get($reservation['sku'], true, $storeId, true);
                        if ($product->getStatus() == Status::STATUS_DISABLED) {
                            $productId                = $product->getId();
                            $productIdsToEnable[$key] = $productId;
                        }
                    }
                }
            }
        }

        if (count($productIdsToDisable) > 0) {
            $this->productAction->updateAttributes(
                $productIdsToDisable,
                ['status' => Status::STATUS_DISABLED],
                (int)$storeId
            );
            $productIdsToDisable = $this->csvFileHelper->generateCsvArray($productIdsToDisable);

            $csvFileName = $this->csvFileHelper->createCsvFileAndWriteToIt(
                $productIdsToDisable,
                'magenmagic:fixinventory_reservation:start_disabled'
            );

            $this->sendmailHelper->sendMail($csvFileName);
        }
        if (count($productIdsToEnable) > 0) {
            $this->productAction->updateAttributes(
                $productIdsToEnable,
                ['status' => Status::STATUS_ENABLED],
                (int)$storeId
            );
            $productIdsToEnable = $this->csvFileHelper->generateCsvArray($productIdsToEnable);

            $csvFileName = $this->csvFileHelper->createCsvFileAndWriteToIt(
                $productIdsToEnable,
                'magenmagic:fixinventory_reservation:start_enabled'
            );

            $this->sendmailHelper->sendMail($csvFileName);
        }
        if ($dryRun == true) {
            if (is_array($skus)) {
                if (count($skus) > 0) {
                    $txtFileName = $this->txtFileHelper->createTxtFileAndWriteToIt(
                        'skus: ' . implode(',', $skus),
                        'magenmagic:fixinventory_reservation:start_enabled:skus'
                    );
                    $this->sendmailHelper->sendMail($txtFileName);
                }
            }
        }

        return $skus;
    }

    private function getInventoryReservations()
    {
        $connection       = $this->resource->getConnection();
        $reservationTable = $this->resource->getTableName('inventory_reservation');

        $select       = $connection->select()->from(
            $reservationTable,
            '*'
        );
        $reservations = $connection->fetchAll($select);

        return $reservations;
    }

    private function getInventoryReservationsSum($sku)
    {
        $connection       = $this->resource->getConnection();
        $reservationTable = $this->resource->getTableName('inventory_reservation');

        $select = $connection->select()->from(
            $reservationTable,
            ['SUM(quantity)']
        )->where('sku=?', $sku);;
        $sum = $connection->fetchOne($select);

        return $sum;
    }

    private function getInventorySourceItemsSum($sku)
    {
        $connection       = $this->resource->getConnection();
        $sourceItemsTable = $this->resource->getTableName('inventory_source_item');

        $select = $connection->select()->from(
            $sourceItemsTable,
            ['SUM(quantity)']
        )->where('sku=?', $sku);
        $sum    = $connection->fetchOne($select);

        return $sum;
    }

    public function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }
}
