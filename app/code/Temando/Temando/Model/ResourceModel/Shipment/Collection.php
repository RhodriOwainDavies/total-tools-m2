<?php

namespace Temando\Temando\Model\ResourceModel\Shipment;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field name.
     *
     * @var string
     */
    protected $_idFieldName = 'shipment_id';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\Shipment', 'Temando\Temando\Model\ResourceModel\Shipment');
    }
}
