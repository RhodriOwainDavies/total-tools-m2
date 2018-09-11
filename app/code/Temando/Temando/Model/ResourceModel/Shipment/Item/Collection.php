<?php

namespace Temando\Temando\Model\ResourceModel\Shipment\Item;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field name.
     *
     * @var string
     */
    protected $_idFieldName = 'shipment_item_id';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\Shipment\Item', 'Temando\Temando\Model\ResourceModel\Shipment\Item');
    }
}
