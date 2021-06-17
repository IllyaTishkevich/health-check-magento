<?php


namespace Magenmagic\PlaceOrderChecker\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class Checker
{
    private $resource;

    public function __construct(
        ResourceConnection $resource
    )
    {
        $this->resource = $resource;
    }

    public function getList() {
        $sql = "SELECT * FROM mm_po_checker WHERE sended_to_healthcheck = 0";
        return $this->resource->getConnection()->fetchAll($sql);
    }

    public function isSendedFlag($entity_id) {
        $connection = $this->resource->getConnection('core_write');
        $query = "UPDATE  mm_po_checker SET sended_to_healthcheck = 1 WHERE  entity_id = '$entity_id'";
        $connection->exec($query);
    }
}
