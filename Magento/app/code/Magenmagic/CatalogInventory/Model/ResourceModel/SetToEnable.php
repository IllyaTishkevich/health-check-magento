<?php

/**
 * @author    MagenMagic Team
 * @copyright Copyright (c) 2020 MagenMagic (https://www.magenmagic.com)
 * @package Magenmagic_CatalogInventory
 */

namespace Magenmagic\CatalogInventory\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;


/**
 * @inheritdoc
 */
class SetToEnable {

    /**
     * @var ResourceConnection
     */
    private $resource;
    private $mmHelper          = null;
    private $prodCollFactory   = null;
    private $productRepository = null;

    public function __construct(
        \Magenmagic\CatalogInventory\Helper\Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $prodCollFactory, //
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, //
        ResourceConnection $resource
    )
    {
        $this->resource          = $resource;
        $this->mmHelper          = $helper;
        $this->prodCollFactory   = $prodCollFactory;
        $this->productRepository = $productRepository;
    }


    /**
     * @inheritdoc
     */
    public function execute()
    {
        if ($confIds = $this->getOutofstockConfigurableIds())
        {
            $confIds = $this->formatData($confIds);
            $this->mmHelper->log('Configurable Product to ENABLE:');
//            $this->mmHelper->log($confIds);

            if ($this->mmHelper->getConfigValue('mmcataloginventory/settoenable/autofix'))
            {
                foreach ($confIds as $prod)
                {
                    $id = $prod['id'];
                    $product = $this->productRepository->getById($id, true);

                    if ($product && $product->getId())
                    {
                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                        $this->productRepository->save($product);
                        $this->mmHelper->log("saved {$product->getId()}");
                    }
                }
            }
        }

        return $confIds;
    }


    private function getOutofstockConfigurableIds()
    {
        $sql = "SELECT e.parent_id, e.product_id, i.qty
                  FROM catalog_product_super_link AS e
                LEFT JOIN cataloginventory_stock_item AS i ON i.product_id = e.product_id
                 WHERE i.qty > 0 AND i.manage_stock > 0
                   AND (EXISTS(SELECT *
                                 FROM catalog_product_entity_int AS s
                                WHERE attribute_id IN (SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'status')
                                  AND s.entity_id = e.parent_id
                                  AND s.value = 2)
                        OR
                        NOT EXISTS (SELECT *
                                      FROM catalog_product_entity_int AS s
                                     WHERE attribute_id IN (SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'status')
                                       AND s.entity_id = e.parent_id))
                   AND EXISTS (SELECT *
                                 FROM catalog_product_entity_int AS s
                                WHERE attribute_id IN (SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'status')
                                  AND s.entity_id = e.product_id
                                  AND s.value = 1)
            ORDER BY e.parent_id
            ";

        return $this->resource->getConnection()->fetchAll($sql);
    }

    protected function formatData($data)
    {
        $result = [];
        foreach ($data as $row)
        {
            $result[$row['parent_id']]['id'] = $row['parent_id'];
            $result[$row['parent_id']]['child_count'][]= "{$row['product_id']} - ".((string) round($row['qty']));
        }

        return $result;
    }
}
