<?php
namespace Temando\Temando\Api\Data;

interface PickupInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const PICKUP_ID   = 'pickup_id';
    const ORDER_ID      = 'order_id';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get order id
     *
     * @return string|null
     */
    public function getOrderId();

    /**
     * Set ID
     *
     * @param int $id
     *
     * @return \Temando\Temando\Api\Data\PickupInterface
     */
    public function setId($id);

    /**
     * Set order_id
     *
     * @param int $order_id
     *
     * @return \Temando\Temando\Api\Data\PickupInterface
     */
    public function setOrderId($order_id);
}
