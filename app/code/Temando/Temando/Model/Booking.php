<?php

namespace Temando\Temando\Model;

use Magento\Framework\Model\AbstractModel;
use Temando\Temando\Api\Data\BookingInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException as CoreException;

class Booking extends AbstractModel implements BookingInterface, IdentityInterface
{
    /**
     * Booking cache tag
     */
    const CACHE_TAG = 'temando_booking';

    /**
     * Temando Shipment.
     *
     * @var \Temando\Temando\Model\Shipment
     */
    protected $_shipment;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_objectManager = $objectManager;
        $this->_shipment = $this->_objectManager->create('\Temando\Temando\Model\Shipment');
        $this->_init('Temando\Temando\Model\ResourceModel\Booking');
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::BOOKING_ID);
    }

    /**
     * Format the response data
     *
     * @param $response
     *
     * @return $this
     */
    public function setResponseData(
        \Temando\Temando\Model\Shipment $shipment,
        \stdClass $resultXml
    ) {
        $this->setShipmentId($shipment->getId());
        $this->setRequestId($resultXml->requestId);
        $this->setConsignmentCode($resultXml->consignmentNumber);
        $this->setLabelDocument($resultXml->labelDocument);
        $this->setConsignmentDocument($resultXml->consignmentDocument);
        return $this;
    }

    /**
     * Get Shipment
     *
     * @return \Temando\Temando\Model\Origin
     */
    public function getShipment()
    {
        $this->_shipment->load($this->getShipmentId());
        return $this->_shipment;
    }
}
