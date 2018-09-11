<?php

namespace Temando\Temando\Model\ResourceModel\Zone;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field name.
     *
     * @var string
     */
    protected $_idFieldName = 'zone_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\Zone', 'Temando\Temando\Model\ResourceModel\Zone');
    }
}
