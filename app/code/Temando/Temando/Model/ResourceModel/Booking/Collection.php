<?php

namespace Temando\Temando\Model\ResourceModel\Booking;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field name
     *
     * @var string
     */
    protected $_idFieldName = 'booking_id';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\Booking', 'Temando\Temando\Model\ResourceModel\Booking');
    }
}
