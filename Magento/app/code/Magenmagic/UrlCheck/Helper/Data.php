<?php

namespace Magenmagic\UrlCheck\Helper;

class Data
{
    private $getUrls;

    public function __construct(
        \Magenmagic\UrlCheck\Model\ResourceModel\GetUrls $getUrls
    )
    {
        $this->getUrls = $getUrls;
    }

    public function checkExist($attr, $storeId, $output)
    {
        $productsUrlPath = $this->getUrls->getFailUrlAttr($attr, $storeId);
        $result = [];

        foreach ($productsUrlPath as $product) {
            $id = $product['id'];
            $sku = $product['sku'];

            $result[$attr] = $id;
            $output->write("$id - $sku - $attr\n");
        }

        return $result;
    }

    public function checkRewrite($storeId, $output)
    {
        $productsRewrite = $this->getUrls->getFailRewrite($storeId);
        $result = [];

        foreach ($productsRewrite as $product) {
            $id = $product['id'];
            $urlKey = $product['value'];
            $target = $product['target_path'];

            $shortTarget = strpos($target, '/') ? substr($target, strripos($target, '/') +1) : $target;

            if($urlKey != $shortTarget){
                $result['url_rewrite'][] = $id;
                $output->write("$id - $urlKey - $target\n");
            }
        }

        return $result;
    }

    public function getProductUnRewrite($output)
    {
        $products = $this->getUrls->getProductUnhavingRewriteUrl();
        $count    = count($products);
        $result   = [];

        foreach ($products as $key => $product) {
            $id = $product['id'];
        
            $result['products_unhaving_rewrite'][] = $id;
            ++$key; 
            $output->write("\r($key / $count) \t \t");
        }
        $output->write("\n");

        return $result;
    }

    public function getProductDelRewrite($output)
    {
        $products = $this->getUrls->getUrlRewritesFromDeletedProducts();
        $count    = count($products);
        $result   = [];

        foreach ($products as $key => $product) {
            $id = $product['id'];
        
            $result['delete_product_rewrites'][] = $id;
            ++$key; 
            $output->write("\r($key / $count) \t \t");
        }
        $output->write("\n");

        return $result;
    }

}
