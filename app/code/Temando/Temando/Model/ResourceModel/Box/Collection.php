<?php

namespace Temando\Temando\Model\ResourceModel\Box;

class Collection extends \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\Box', 'Temando\Temando\Model\ResourceModel\Box');
    }
    
    /**
     * Returns sum of box values
     *
     * @return float
     */
    public function getTotalGoodsValue()
    {
        $totalValue = 0;
        foreach ($this->getItems() as $box) {
            /* @var $box Temando_Temando_Model_Box */
            $totalValue += $box->getValue();
        }
        return (float)$totalValue;
    }
}
