<?php

namespace Temando\Temando\Model\Shipment;

use Magento\Framework\Model\AbstractModel;
use Temando\Temando\Api\Data\ShipmentitemInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException as CoreException;
use Temando\Temando\Api\Data\timestamp;

class Item extends AbstractModel implements ShipmentitemInterface
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'temando_shipment_item';

    /**
     * Cache tag.
     *
     * @var string
     */
    protected $_cacheTag = 'temando_shipment_item';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'temando_shipment_item';
    
    protected function _construct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_orderItem = $objectManager->create('Magento\Sales\Model\Order\Item');
        $this->_init('Temando\Temando\Model\ResourceModel\Shipment\Item');
    }

    /**
     * Return unique ID(s) for each object in system.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Store Id.
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Get shipment id.
     *
     * @return int
     */
    public function getShipmentId()
    {
        return $this->getData(self::SHIPMENT_ID);
    }

    /**
     * Get Order Item Id.
     *
     * @return int
     */
    public function getOrderItemId()
    {
        return $this->getData(self::ORDER_ITEM_ID);
    }

    /**
     * Get warehouse - unique identifier on Temando API.
     *
     * @return string
     */
    public function getWarehouse()
    {
        return $this->getData(self::WAREHOUSE);
    }

    /**
     * Get Sku.
     *
     * @return string
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * Get sku type.
     *
     * @return string
     */
    public function getSkuType()
    {
        return $this->getData(self::SKU_TYPE);
    }

    /**
     * Get quantity To Ship.
     *
     * @return int
     */
    public function getQtyToShip()
    {
        return $this->getData(self::QTY_TO_SHIP);
    }

    /**
     * Get quantity Ordered.
     *
     * @return int
     */
    public function getQtyOrdered()
    {
        return $this->getData(self::QTY_ORDERED);
    }

    /**
     * Get quantity shipped.
     *
     * @return int
     */
    public function getQtyShipped()
    {
        return $this->getData(self::QTY_SHIPPED);
    }

    /**
     * Get Status.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Get user id.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * Get fulfilled at.
     *
     * @return timestamp
     */
    public function getFulfilledAt()
    {
        return $this->getData(self::FULFILLED_AT);
    }

    /**
     * Get created at.
     *
     * @return timestamp
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set store Id.
     *
     * @param string $store_id
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setStoreId($store_id)
    {
        return $this->setData(self::STORE_ID, $store_id);
    }

    /**
     * Set shipment Id.
     *
     * @param string $shipment_id
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setShipmentId($shipment_id)
    {
        return $this->setData(self::SHIPMENT_ID, $shipment_id);
    }

    /**
     * Set order item id.
     *
     * @param string $order_item_id
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setOrderItemId($order_item_id)
    {
        return $this->setData(self::ORDER_ITEM_ID, $order_item_id);
    }

    /**
     * Set warehouse name.
     *
     * @param string $warehouse
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setWarehouse($warehouse)
    {
        return $this->setData(self::WAREHOUSE, $warehouse);
    }

    /**
     * Set sku.
     *
     * @param string $sku
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * Set sku type.
     *
     * @param string $sku
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setSkuType($sku_type)
    {
        return $this->setData(self::SKU_TYPE, $sku_type);
    }

    /**
     * Set quantity to ship.
     *
     * @param int $qty_to_ship
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setQtyToShip($qty_to_ship)
    {
        return $this->setData(self::QTY_TO_SHIP, $qty_to_ship);
    }

    /**
     * Set quantity ordered
     *
     * @param int $qty_ordered
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setQtyOrdered($qty_ordered)
    {
        return $this->setData(self::QTY_ORDERED, $qty_ordered);
    }

    /**
     * Set quantity shipped
     *
     * @param int $qty_to_ship
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setQtyShipped($qty_shipped)
    {
        return $this->setData(self::QTY_SHIPPED, $qty_shipped);
    }

    /**
     * Set status
     *
     * @param int $status
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set user id
     *
     * @param int $user_id
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setUserId($user_id)
    {
        return $this->setData(self::USER_ID, $user_id);
    }

    /**
     * Set fulfilled at
     *
     * @param timestamp $fulfilled_at
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setFulfilledAt($fulfilled_at)
    {
        return $this->setData(self::FULFILLED_AT, $fulfilled_at);
    }

    /**
     * Set created at
     *
     * @param timestamp $created_at
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
    }
    
    /**
     * Returns sales order line item object
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function getOrderItem()
    {
        return $this->_orderItem->load($this->getOrderItemId());
    }
}
