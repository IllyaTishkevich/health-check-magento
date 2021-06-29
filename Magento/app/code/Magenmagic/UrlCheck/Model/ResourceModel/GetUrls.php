<?php

namespace Magenmagic\UrlCheck\Model\ResourceModel;

class GetUrls
{
    private $resource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->resource = $resource;
    }
    
    public function getFailUrlAttr($attr, $storeId)
    {
        $query = "SELECT entity_id as id, sku
                    FROM catalog_product_entity WHERE entity_id NOT IN 
                        (SELECT distinct entity_id FROM catalog_product_entity_varchar cev JOIN eav_attribute as eav 
                        ON eav.attribute_id = cev.attribute_id WHERE eav.attribute_code = '$attr'
                        AND cev.value != '' AND cev.store_id != $storeId)
                ";
        
        return $this->resource->getConnection()->fetchAll($query);
    }

    public function getFailRewrite($storeId)
    {
        $query = "SELECT re.url_rewrite_id as id, cev.value, re.target_path
                    FROM catalog_product_entity_varchar cev 
                  JOIN url_rewrite re ON cev.entity_id = re.entity_id 
                    WHERE cev.attribute_id = (SELECT attribute_id FROM eav_attribute WHERE attribute_code = 'url_key' 
                        AND entity_type_id = (SELECT entity_type_id FROM eav_entity_type WHERE entity_type_code = 'catalog_product'))
                    AND cev.value != '' AND re.store_id = $storeId AND re.redirect_type = 301 
                    AND (re.metadata NOT REGEXP 'category_id' OR re.metadata IS NULL)";

        return $this->resource->getConnection()->fetchAll($query);
    }

    public function getProductUnhavingRewriteUrl()
    {
        $query = "SELECT entity_id as id FROM catalog_product_entity 
                    WHERE entity_id NOT IN 
                        (SELECT entity_id FROM url_rewrite GROUP BY entity_id)
        ";

        return $this->resource->getConnection()->fetchAll($query);
    }

    public function getUrlRewritesFromDeletedProducts()
    {
        $query = "SELECT url_rewrite_id as id FROM url_rewrite 
                    WHERE url_rewrite_id NOT IN 
                        (SELECT url_rewrite_id FROM url_rewrite re 
                            LEFT JOIN catalog_product_entity ce ON ce.entity_id = re.entity_id 
                                WHERE ce.entity_id = re.entity_id )";

        return $this->resource->getConnection()->fetchAll($query);
    }
}
