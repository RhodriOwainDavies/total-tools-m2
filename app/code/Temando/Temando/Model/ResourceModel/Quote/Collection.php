<?php

namespace Temando\Temando\Model\ResourceModel\Quote;

class Collection extends \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\Quote', 'Temando\Temando\Model\ResourceModel\Quote');
    }
}
