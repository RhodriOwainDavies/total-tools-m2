<?php

namespace Temando\Temando\Model\ResourceModel\Origin;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field name
     *
     * @var string
     */
    protected $_idFieldName = 'origin_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\Origin', 'Temando\Temando\Model\ResourceModel\Origin');
    }

    /**
     * Prepare JSON.
     *
     * @param array $mapJsonFields
     */
    public function prepareJson()
    {
        $originArray = [];

        foreach ($this as $item) {
            $originArray[] = $item->getData();
        }

        return $originArray;
    }
    
    
    /**
     * Add latitude and longitude to filter by distance.
     *
     * @param $lat
     * @param $lng
     * @param $distance
     *
     * @return $this
     */
    public function addLatLngToFilterDistance($lat, $lng, $distance = null)
    {
        $expression = "(1609.34*((acos(sin(({{lat}}*pi()/180)) * sin((`{{latitude}}`*pi()/180))+cos(($lat *pi()/180))"
            . " * cos((`{{latitude}}`*pi()/180)) * cos((({{lng}} - `{{longitude}}`)*pi()/180))))*180/pi())*60*1.1515)";
        $this->addExpressionFieldToSelect(
            'distance',
            $expression,
            [
                'latitude' => 'latitude',
                'longitude' => 'longitude',
                'lat' => $lat,
                'lng' => $lng
            ]
        );

        if ($distance) {
            $this->getSelect()->having('distance <= ?', $distance);
        }

        return $this;
    }
    
    /**
     * Get Select Count SQL.
     *
     * @return \Magento\Framework\DB\Select
     *
     * @throws \Zend_Db_Select_Exception
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(\Zend_Db_Select::ORDER);
        $countSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(\Zend_Db_Select::COLUMNS);

        if (count($this->getSelect()->getPart(\Zend_Db_Select::GROUP)) > 0) {
            $countSelect->reset(\Zend_Db_Select::GROUP);
            $countSelect->distinct(true);
            $group = $this->getSelect()->getPart(\Zend_Db_Select::GROUP);
            $countSelect->columns('COUNT(DISTINCT ' . implode(', ', $group) . ')');
        } elseif (count($this->getSelect()->getPart(\Zend_Db_Select::HAVING)) > 0) {
            $connection = $this->getResource()->getConnection();

            return $connection->select()->from(['select_store' => $this->getSelect()], 'COUNT(*)');
        } else {
            $countSelect->columns('COUNT(*)');
        }

        return $countSelect;
    }
}
