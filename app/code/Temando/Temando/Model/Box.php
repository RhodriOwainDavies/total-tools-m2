<?php

namespace Temando\Temando\Model;

use Magento\Framework\Model\AbstractModel;

class Box extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\ResourceModel\Box');
    }
}
