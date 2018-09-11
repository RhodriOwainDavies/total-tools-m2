<?php

namespace Temando\Temando\Model;

use Magento\Framework\Model\AbstractModel;

class Carrier extends AbstractModel
{
    const FLAT_RATE = 'flat';
    const FREE      = 'free';

    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\ResourceModel\Carrier');
    }
}
