<?php
namespace Temando\Temando\Api\Data;

interface ShipmentitemInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const SHIPMENT_ITEM_ID  = 'shipment_item_id';
    const STORE_ID          = 'store_id';
    const SHIPMENT_ID       = 'shipment_id';
    const ORDER_ITEM_ID     = 'order_item_id';
    const WAREHOUSE         = 'warehouse';
    const SKU               = 'sku';
    const SKU_TYPE          = 'sku_type';
    const QTY_TO_SHIP       = 'qty_to_ship';
    const QTY_ORDERED       = 'qty_ordered';
    const QTY_SHIPPED       = 'qty_shipped';
    const STATUS            = 'status';
    const USER_ID           = 'user_id';
    const FULFILLED_AT      = 'fulfilled_at';
    const CREATED_AT        = 'created_at';

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Get Store Id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Get shipment id
     *
     * @return int
     */
    public function getShipmentId();

    /**
     * Get Order Item Id
     *
     * @return int
     */
    public function getOrderItemId();

    /**
     * Get warehouse - unique identifier on Temando API
     *
     * @return string
     */
    public function getWarehouse();

    /**
     * Get Sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Get sku type
     *
     * @return string
     */
    public function getSkuType();

    /**
     * Get quantity To Ship
     *
     * @return int
     */
    public function getQtyToShip();

    /**
     * Get quantity Ordered
     *
     * @return int
     */
    public function getQtyOrdered();

    /**
     * Get quantity shipped
     *
     * @return int
     */
    public function getQtyShipped();

    /**
     * Get Status
     *
     * @return int
     */
    public function getStatus();

    /**
     * Get user id
     *
     * @return int
     */
    public function getUserId();

    /**
     * Get fulfilled at
     *
     * @return timestamp
     */
    public function getFulfilledAt();

    /**
     * Get created at
     *
     * @return timestamp
     */
    public function getCreatedAt();


    /**
     * Set ID
     *
     * @param int $id
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setId($id);

    /**
     * Set store Id
     *
     * @param string $store_id
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setStoreId($store_id);

    /**
     * Set shipment Id
     *
     * @param string $shipment_id
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setShipmentId($shipment_id);

    /**
     * Set order item id
     *
     * @param string $order_item_id
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setOrderItemId($order_item_id);

    /**
     * Set warehouse name
     *
     * @param string $warehouse
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setWarehouse($warehouse);

    /**
     * Set sku
     *
     * @param string $sku
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setSku($sku);

    /**
     * Set sku type
     *
     * @param string $sku
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setSkuType($sku_type);

    /**
     * Set quantity to ship
     *
     * @param int $qty_to_ship
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setQtyToShip($qty_to_ship);

    /**
     * Set quantity ordered
     *
     * @param int $qty_ordered
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setQtyOrdered($qty_ordered);

    /**
     * Set quantity shipped
     *
     * @param int $qty_to_ship
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setQtyShipped($qty_shipped);

    /**
     * Set status
     *
     * @param int $status
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setStatus($status);

    /**
     * Set user id
     *
     * @param int $user_id
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setUserId($user_id);

    /**
     * Set fulfilled at
     *
     * @param timestamp $fulfilled_at
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setFulfilledAt($fulfilled_at);

    /**
     * Set created at
     *
     * @param timestamp $created_at
     *
     * @return \Temando\Temando\Api\Data\ZoneInterface
     */
    public function setCreatedAt($created_at);
}
