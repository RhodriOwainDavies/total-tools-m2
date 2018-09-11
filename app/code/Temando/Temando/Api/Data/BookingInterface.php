<?php
namespace Temando\Temando\Api\Data;

interface BookingInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const SHIPMENT_ID   = 'shipment_id';
    const BOOKING_ID      = 'booking_id';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     *
     * @return \Temando\Temando\Api\Data\ShipmentInterface
     */
    public function setId($id);
}
