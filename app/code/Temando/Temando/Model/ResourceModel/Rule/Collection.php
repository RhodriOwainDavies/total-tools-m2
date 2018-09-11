<?php

namespace Temando\Temando\Model\ResourceModel\Rule;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field name.
     *
     * @var string
     */
    protected $_idFieldName = 'rule_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\Rule', 'Temando\Temando\Model\ResourceModel\Rule');
    }
}
