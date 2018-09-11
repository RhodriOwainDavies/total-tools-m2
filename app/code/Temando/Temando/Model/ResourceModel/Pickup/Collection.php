<?php

namespace Temando\Temando\Model\ResourceModel\Pickup;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field name.
     *
     * @var string
     */
    protected $_idFieldName = 'pickup_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\Pickup', 'Temando\Temando\Model\ResourceModel\Pickup');
    }
}
