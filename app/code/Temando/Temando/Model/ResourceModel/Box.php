<?php

namespace Temando\Temando\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Box extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('temando_box', 'id');
    }
}
