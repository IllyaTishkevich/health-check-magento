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
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

class Products extends \Magento\Framework\App\Helper\AbstractHelper
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

    const CONFIG_MODULE_ENABLED = 'mm_cron_jobs/mmcheckinventory/enabled';

    const AUTO_ENABLE_PRODUCTS  = 'mm_cron_jobs/mmcheckinventory/auto_enable_products';

    public function __construct(
        Context $context, StoreManagerInterface $storeManager, LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, ResourceConnection $resource,
        \Magento\Framework\Serialize\Serializer\Json $json, $productAction = null, Sendmail $sendmailHelper,
        CsvFile $csvFileHelper
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
        parent::__construct($context);
    }

    public function enableProducts($storeId, $dryRun = false, $productIdsToEnable = [])
    {
        if ($this->isAutoEnableProducts() && sizeof($productIdsToEnable) == 0) {
            $productIdsToEnable = $this->getDisabledProducts($storeId);
        }
        if ($this->isAutoEnableProducts() && sizeof($productIdsToEnable) > 0) {
            if (!$dryRun) {
                $ids = [];
                foreach ($productIdsToEnable as $index => $item) {
                    $ids[$index] = $item['product_id'];
                }
                $this->productAction->updateAttributes(
                    $ids,
                    ['status' => Status::STATUS_ENABLED],
                    (int)$storeId
                );
            }
            $productIdsToEnable = $this->csvFileHelper->generateCsvArray($productIdsToEnable);

            $csvFileName =
                $this->csvFileHelper->createCsvFileAndWriteToIt($productIdsToEnable, 'magenmagic:enable_products_');

            $this->sendmailHelper->sendMail($csvFileName);
        }

        $result = [];
        foreach ($productIdsToEnable as $index => $item) {
            if ($index == 0) {
                continue;
            }
            $result[$index] = $item[0];
        }

        return $result;
    }

    private function getDisabledProducts($storeId = 0)
    {
        $connection = $this->resource->getConnection();

        $cataloginventoryStockStatusTable = $this->resource->getTableName('cataloginventory_stock_status');
        $catalogProductEntityTable        = $this->resource->getTableName('catalog_product_entity');
        $eavAttributeTable                = $this->resource->getTableName('eav_attribute');
        $catalogProductEntityIntTable     = $this->resource->getTableName('catalog_product_entity_int');
        $attributeIdQuery                 = $connection->select()->from(
            ['eav_attribute' => $eavAttributeTable],
            'attribute_id'
        )->where('eav_attribute.attribute_code = (?)', 'status');
        $attributeId                      = $connection->fetchAll($attributeIdQuery);
        $catalogProductEntityIntQuery     = $connection->select()->from(
            ['catalog_product_entity_int' => $catalogProductEntityIntTable],
            ['product_id' => 'entity_id']
        )->where('attribute_id IN (?) and value=2', $attributeId)->where('store_id=(?)', $storeId);

        $catalogProductEntityInt = $connection->fetchAll($catalogProductEntityIntQuery);

        $selectQuery = $connection->select()->from(
            ['cataloginventory_stock_status' => $cataloginventoryStockStatusTable],
            [
                'product_id' => 'product_id',
            ]
        )->joinLeft(
            ['catalog_product_entity' => $catalogProductEntityTable],
            'cataloginventory_stock_status.product_id = catalog_product_entity.entity_id',
            []
        )->where(
            'cataloginventory_stock_status.qty > 0
          and cataloginventory_stock_status.stock_status > 0
          and cataloginventory_stock_status.product_id IN (?)',
            $catalogProductEntityInt
        );

        $disabledProducts = $connection->fetchAll($selectQuery);

        return $disabledProducts;
    }

    private function isAutoEnableProducts()
    {
        return $this->scopeConfig->isSetFlag(
            self::AUTO_ENABLE_PRODUCTS,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_MODULE_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /*
 * get Current store id
 */
    private function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /*
     * get Current store Info
     */
    private function getStore()
    {
        return $this->storeManager->getStore();
    }
}
