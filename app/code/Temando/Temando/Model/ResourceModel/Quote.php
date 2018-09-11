<?php

namespace Temando\Temando\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Quote extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('temando_quote', 'quote_id');
    }
}
