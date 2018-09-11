<?php

namespace Temando\Temando\Model\ResourceModel\Carrier;

class Collection extends \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection
{
    /**
     * ID Field name
     *
     * @var string
     */
    protected $_idFieldName = 'carrier_id';
    
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\Carrier', 'Temando\Temando\Model\ResourceModel\Carrier');
    }
}
