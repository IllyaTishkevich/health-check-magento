<?php
namespace Magenmagic\GalleryCheck\Helper;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    protected $productCollection;
    protected $product;

    public function __construct(
        CollectionFactory $productCollection,
        Product $product
    ) {
        $this->productCollection = $productCollection;
        $this->product = $product;
    }

    public function getHiddenImageId()
    {
        $imagetypes = ['image', 'small_image', 'thumbnail'];
        $noSelectIds = [];
        $noImageIds = [];
        $ids = [];
        $products = [];

        $collection = $this->productCollection->create();
        $collection->addAttributeToSelect('entity_id');
        $collection->setPageSize(8);
        $collection->addAttributeToFilter('status', ['in' => '1']);
        //$collection->addAttributeToFilter('type_id', \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE);// get only simple products
        //$collection->addAttributeToFilter('type_id', 'configurable'); //get only configurable products
        //$collection->joinField('stock_item', 'cataloginventory_stock_item', 'is_in_stock', 'product_id=entity_id', 'is_in_stock=1'); //get in stock products
        //$collection->joinField('stock_item', 'cataloginventory_stock_item', 'is_in_stock', 'product_id=entity_id', 'is_in_stock=0'); //get out of stock products

        //$filesystem = $objectManager->create('Magento\Framework\Filesystem');
        //$mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        //$mediaPath = $mediaDirectory->getAbsolutePath();

        $flagSave = false;

        foreach ($collection as $p) {
            $product = $this->product->load($p->getId());

            $images = [];
            foreach ($imagetypes as $imgtype) {
                $images[] = $product->getData($imgtype);
            }

            $arr = $product->getData('media_gallery');

            if (isset($arr['images']) && empty($arr['images'])) {
                $noImageIds[] = $product->getId();
            }

            $images = array_unique($images);

            if (isset($arr['images']) && !empty($arr['images'])) {
                foreach ($arr['images'] as $k => $img) {
                    if (in_array('no_selection', $images)) {
                        $noSelectIds[] = $product->getId();
                        $arr['images'][$k]['disabled'] = 0;
                        $flagSave = true;
                        if ($flagSave) {
                            // echo " found id " . $product->getId() . " - ";
                            // $product->setStoreId(0)->setMediaGallery($arr)->save();  // save changes - disable Hide from Product Page
                            //echo " done";
                        }
                    }
                }
            }

            if (isset($arr['images']) && !empty($arr['images'])) {
                foreach ($arr['images'] as $k => $img) {
                    if (in_array($img['file'], $images)) {
                        if ($img['disabled']) {
                            $ids[] = $product->getId();
                            $arr['images'][$k]['disabled'] = 0;
                            $flagSave = true;
                            if ($flagSave) {
                                //  echo " begin - id ". $product->getId();
                                // $product->setStoreId(0)->setMediaGallery($arr)->save(); // save changes - disable Hide from Product Page
                                //echo " done";
                            }
                        }
                    }
                }
            }
        }

        $products[] = [
            'products_selected_base_small_thumbnail' => array_values(array_unique($ids)),
            'products_without_selected_base_small_thumbnail' => array_values(array_unique($noSelectIds)),
            'products_without_images' => array_values(array_unique($noImageIds))
        ];
        
        return json_encode($products);
    }
}
