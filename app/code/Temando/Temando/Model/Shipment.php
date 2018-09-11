<?php

namespace Temando\Temando\Model;

use Magento\Framework\Model\AbstractModel;
use Temando\Temando\Api\Data\ShipmentInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException as CoreException;

class Shipment extends AbstractModel implements ShipmentInterface, IdentityInterface
{
    /**
     * Shipment cache tag
     */
    const CACHE_TAG = 'temando_shipment';

    /**
     * Temando Shipment.
     *
     * @var string
     */
    protected $_cacheTag = 'temando_shipment';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'temando_shipment';

    /**
     * Magento sales order object.
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_salesOrder;

    /**
     * Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * Temando Origin object
     *
     * @var \Temando\Temando\Model\Origin
     */
    protected $_origin;

    /**
     * Temando API Request.
     *
     * @var \Temando\Temando\Model\Api\Request
     */
    protected $_request;

    /**
     * Store Manager Interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_currency;

    /**
     * Temando Shipment Item Collection
     *
     * @var \Temando\Temando\Model\ResourceModel\Shipment\Item\Collection
     */
    protected $_shipmentItems;

    /**
     * Magento Object Manager.
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * Temando Box Collection
     *
     * @var \Temando\Temando\Model\ResourceModel\Box\Collection
     */
    protected $_shipmentBoxes;

    /**
     * Temando Shipment Booking Collection
     *
     * @var \Temando\Temando\Model\ResourceModel\Booking\Collection
     */
    protected $_shipmentBookings;

    /**
     * Magento Quote Collection.
     *
     * @var \Temando\Temando\Model\ResourceModel\Quote\Collection
     */
    protected $_quoteCollection;

    /**
     * Magento Shipment.
     *
     * @var \Magento\Sales\Model\Order\Shipment
     */
    protected $_orderShipment;

    /**
     * Magento Shipment Track.
     *
     * @var \Magento\Sales\Model\Order\Shipment\Track
     */
    protected $_shipmentTrack;

    /**
     * Magento Sales Converter.
     *
     * @var \Magento\Sales\Model\Convert\Order
     */
    protected $_convertOrder;

    /**
     * Magento Product Factory.
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Custom Temando Logger.
     *
     * @var \Temando\Temando\Logger\General\Logger
     */
    protected $_logger;
             
    protected function _construct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_objectManager = $objectManager;
        $this->_salesOrder = $objectManager->create('Magento\Sales\Model\Order');
        $this->_helper = $objectManager->create('Temando\Temando\Helper\Data');
        $this->_origin = $objectManager->create('Temando\Temando\Model\Origin');
        $this->_request = $objectManager->create('Temando\Temando\Model\Api\Request');
        $this->_shipmentItems = $objectManager->create('Temando\Temando\Model\ResourceModel\Shipment\Item\Collection');
        $this->_shipmentBoxes = $objectManager->create('Temando\Temando\Model\ResourceModel\Box\Collection');
        $this->_shipmentBookings = $objectManager->create('Temando\Temando\Model\ResourceModel\Booking\Collection');
        $this->_currency = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
        $this->_quoteCollection = $objectManager->create('Temando\Temando\Model\ResourceModel\Quote\Collection');
        $this->_orderShipment = $objectManager->create('Magento\Sales\Model\Order\Shipment');
        $this->_shipmentTrack = $objectManager->create('Magento\Sales\Model\Order\Shipment\Track');
        $this->_convertOrder = $objectManager->create('Magento\Sales\Model\Convert\Order');
        $this->_productFactory = $objectManager->create('Magento\Catalog\Model\ProductFactory');
        $this->_logger = $objectManager->create('Temando\Temando\Logger\General\Logger');
        $this->_init('Temando\Temando\Model\ResourceModel\Shipment');
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
        return $this->getData(self::SHIPMENT_ID);
    }

    /**
     * Get order id
     *
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Set ID
     *
     * @param int $shipment_id
     *
     * @return \Temando\Temando\Api\Data\ShipmentInterface
     */
    public function setId($shipment_id)
    {
        return $this->setData(self::SHIPMENT_ID, $shipment_id);
    }

    /**
     * Set order id.
     *
     * @param int $order_id
     *
     * @return \Temando\Temando\Api\Data\ShipmentInterface
     */
    public function setOrderId($order_id)
    {
        return $this->setData(self::ORDER_ID, $order_id);
    }

    /**
     * Get Order
     *
     * @return \Magento\Sales\Model\Order.
     */
    public function getOrder()
    {
        $this->_salesOrder->load($this->getOrderId());
        return $this->_salesOrder;
    }

    /**
     * Get Origin.
     *
     * @return \Temando\Temando\Model\Origin
     */
    public function getOrigin()
    {
        $this->_origin->load($this->getOriginId());
        return $this->_origin;
    }

    /**
     * Save all items.
     *
     * @return \Temando\Temando\Model\Shipment
     */
    public function saveAllItems($filter = array())
    {
        foreach ($this->getOrder()->getAllVisibleItems() as $item) {
            if (!empty($filter) && !array_key_exists($item->getSku(), $filter)) {
                continue;
            }
            /* @var $shipItem \Temando\Temando\Model\Shipment\Item */
            $shipItem = $this->_objectManager->create('Temando\Temando\Model\Shipment\Item');
            $qtyToShip = $item->getQtyOrdered();
            if (array_key_exists($item->getSku(), $filter)) {
                $qtyToShip = $filter[$item->getSku()];
            }

            $_product = $this->_productFactory->create();
            $_product = $_product->loadByAttribute('sku', $item->getSku());
            $stockAvailabilityCode = $_product->getAttributeText('stock_availability_code');
            $shipmentItemStatus = \Temando\Temando\Model\System\Config\Source\Shipment\Status::PENDING;
            if (($stockAvailabilityCode == "OD") && (!$this->_origin->hasStock(array($item->getSku() => $qtyToShip)))) {
                $shipmentItemStatus = \Temando\Temando\Model\System\Config\Source\Shipment\Status::BACK_ORDER;
            }

            $shipItem
                ->setStoreId($this->getStoreId())
                ->setShipmentId($this->getId())
                ->setOrderItemId($item->getItemId())
                ->setWarehouse($this->getOrigin()->getName())
                ->setSku($item->getSku())
                ->setQtyToShip($qtyToShip)
                ->setQtyOrdered($qtyToShip)
                ->setQtyShipped(0)
                ->setStatus($shipmentItemStatus)
                ->setCreatedAt(time())
                ->save();
        }
        return $this;
    }

    /**
     * Get all items.
     *
     * @return \Temando\Temando\Model\ResourceModel\Shipment\Item\Collection
     */
    public function getAllItems()
    {
        $this->_shipmentItems = $this->_objectManager->create(
            'Temando\Temando\Model\ResourceModel\Shipment\Item\Collection'
        );
        return $this->_shipmentItems->addFieldToFilter('shipment_id', $this->getId())->load();
    }

    /**
     * Get quotes.
     *
     * @return array
     */
    public function getQuotes()
    {
        $this->_quoteCollection->getSelect()->joinLeft(
            ['temando_carrier'=>$this->_quoteCollection->getTable('temando_carrier')],
            'main_table.carrier_id = temando_carrier.carrier_id',
            ['carrier_name'=>'temando_carrier.company_name']
        );

        return $this->_quoteCollection->addFieldToFilter('magento_quote_id', $this->getOrder()->getQuoteId())->load();
    }

    /**
     * Get boxes.
     *
     * @return \Temando\Temando\Model\ResourceModel\Box\Collection
     */
    public function getBoxes()
    {
        return $this->_shipmentBoxes
            ->addFieldToFilter('shipment_id', $this->getId())
            ->load();
    }

    /**
     * Get bookings.
     *
     * @return \Temando\Temando\Model\ResourceModel\Booking\Collection
     */
    public function getBookings()
    {
        return $this->_shipmentBookings
            ->addFieldToFilter('shipment_id', $this->getId())
            ->load();
    }

    /**
     * Gets Temando quotes for this shipment.
     *
     * @return \Temando\Temando\Model\ResourceModel\Quote\Collection
     */
    public function fetchQuotes()
    {
        /* @var $shipment \Temando\Temando\Model\Shipment */
        $shipment = $this->load($this->getId());
      
        /* @var $origin \Temando\Temando\Model\Origin */
        $origin = $this->_origin->load($this->getOriginId());

        /* @var $request Temando_Temando_Model_Api_Request */
        $this->_request
            ->setConnectionParams($origin->getTemandoProfile())
            ->setMagentoQuoteId($this->getOrder()->getData('quote_id'))
            ->setGoodsCurrency($this->_currency->getStore()->getCurrentCurrency()->getCode())
            ->setDestination(
                $this->getDestinationCountry(),
                $this->getDestinationPostcode(),
                $this->getDestinationCity(),
                $this->getDestinationStreet(),
                $this->getDestinationType()
            )
            ->setOrigin($origin->getName(), $origin->getCountry())
            ->setItems($this->getBoxes())
            ->setArticles($this->getArticles(false))
            ->setDeliveryOptions($this->getDeliveryOptions())
            ->setReady(null);
        return $this->_request->getQuotes();
    }
    
    /**
     * Returns array of allocated shipment articles to boxes
     *
     * @param boolean $isBooking Is this a shipment booking call?
     * @param int $warehouseId The shipment origin ID
     *
     * @return array Allocated articles to shipment boxes
     */
    public function getArticles($isBooking = true, $warehouseId = null)
    {
        $articles = array();
        $articleGoodsValue = 0;
        $origin = $this->_origin->load($warehouseId);
        if (!$origin->getId()) {
            $origin = $this->getOrigin();
        }
        foreach ($this->getAllItems() as $shipmentItem) {
            $item = $shipmentItem->getOrderItem();

            if ($item->getIsVirtual()) {
                continue;
            }
            if ($item->getProduct() && $item->getProduct()->isVirtual()) {
                continue;
            }
            if ($item->getFreeShipping() && !$isBooking) {
                continue;
            }

            $packages = $this->_helper->getProductArticles(
                $item,
                $origin->getCountry() != $this->getDestinationCountry()
            );
            for ($i=1; $i<=$shipmentItem->getQtyToShip(); $i++) {
                foreach ($packages as $package) {
                /*    if ($origin->getCountry() != $this->getDestinationCountry()) {
                        $articles[] = array(
                            'description'   => $package['description'],
                            'sku'       => $item->getSku(),
                            'hs'        => Mage::helper('temando')->getHsCode(
                                $item->getSku(),
                                $origin->getCountry(),
                                $this->getDestinationCountry()
                            ),
                            'countryOfOrigin'   => $package['coo'],
                            'countryOfManufacture'  => $package['com'],
                            'composition'   => $package['composition'],
                            'goodsCurrency'     => $this->getOrder()->getStore()->getCurrentCurrencyCode(),
                            'goodsValue'    => $package['value']
                        );
                    } else {*/
                        $articles[] = array(
                            'description'   => $package['description'],
                            'sku'       => $item->getSku(),
                            'goodsCurrency'     => $this->getOrder()->getStore()->getCurrentCurrencyCode(),
                            'goodsValue'    => $package['value']
                        );
                    /*}*/
                    $articleGoodsValue += $package['value'];
                }
            }
        }
        
        return $articles;
    }

    /**
     * Process makeBooking request response
     *
     * @param stdClass $resultXml
     * @param Temando_Temando_Model_Quote $quote
     *
     * @return void
     *
     * @throws Exception $e
     */
    public function processBookingResult(\stdClass $resultXml, \Temando\Temando\Model\Quote $quote)
    {
        try {
            $this->_prepareBookingXml($resultXml);
            $booking = $this->_objectManager->create('Temando\Temando\Model\Booking');
            $booking->setResponseData($this, $resultXml);
            $booking->save();

            $this->convertToMagentoShipment($resultXml, $quote, true);
            if ($this->getOrder()->getIsVirtual() || $this->getOrder()->isCanceled()) {
                return;
            }
            
            $this->setReadyDate(date('Y-m-d H:i:s'))
                  ->setStatus(\Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED)
                  ->save();
        } catch (Exception $e) {
            $this->_logger->debug('Failed to process booking result : ' . $e->getMessage());
        }
    }

    /**
     * Prepares booking request response for processing
     *
     * @param stdClass $resultXml
     *
     * @return \Temando\Temando\Model\Shipment
     */
    protected function _prepareBookingXml($resultXml)
    {
        if (!isset($resultXml->bookingNumber)) {
            $resultXml->bookingNumber = null;
        }
        if (!isset($resultXml->consignmentNumber)) {
            $resultXml->consignmentNumber = null;
        }
        if (!isset($resultXml->consignmentDocument)) {
            $resultXml->consignmentDocument = null;
        }
        if (!isset($resultXml->consignmentDocumentType)) {
            $resultXml->consignmentDocumentType = null;
        }
        if (!isset($resultXml->requestId)) {
            $resultXml->requestId = null;
        }
        if (!isset($resultXml->labelDocument)) {
            $resultXml->labelDocument = null;
        }
        if (!isset($resultXml->labelDocumentType)) {
            $resultXml->labelDocumentType = '';
        }
        if (!isset($resultXml->commercialInvoiceDocument)) {
            $resultXml->commercialInvoiceDocument = null;
        }
        if (!isset($resultXml->commercialInvoiceDocumentType)) {
            $resultXml->commercialInvoiceDocumentType = '';
        }
        if (isset($resultXml->anytime)) {
            $this->setReadyDate((string) $resultXml->anytime->readyDate);
            $this->setReadyTime((string) $resultXml->anytime->readyTime);
        }
        return $this;
    }

    /**
     * Converts this shipment to Magento shipment, adds tracking and sends out
     * shipment confirmation email
     *
     * @param strClass $resultXml
     * @param Temando_Temando_Model_Quote $quote
     * @param boolean $sendMail Send confirmation email?
     *
     * @return void
     */
    public function convertToMagentoShipment($resultXml, \Temando\Temando\Model\Quote $quote, $sendMail = true)
    {
        $mageShipment = $this->_convertOrder->toShipment($this->getOrder());
        $totalQty = $shipQty = 0;
        $processedItems = array();
        foreach ($this->getOrder()->getAllItems() as $item) {
            $shipmentItem = $this->_objectManager->create(
                'Temando\Temando\Model\ResourceModel\Shipment\Item\Collection'
            );
            $shipmentItem = $shipmentItem->addFieldToFilter('order_item_id', $item->getId())->getFirstItem();

            $shipQty = (int)$shipmentItem->getQtyToShip();

            if ($shipQty && !$item->getIsVirtual()) {
                $mageShipmentItem = $this->_convertOrder->itemToShipmentItem($item);
                $mageShipmentItem->setQty($shipQty);
                $mageShipment->addItem($mageShipmentItem);
                $totalQty += $shipQty;
                $shipmentItem
                    ->setCurrentAdminUser()
                    ->setQtyShipped($shipQty)
                    ->setQtyToShip(0)
                    ->setStatus(\Temando\Temando\Model\System\Config\Source\Shipment\Status::BOOKED)
                    ->setFulfilledAt(time());
                $processedItems[] = $shipmentItem;
            }
        }
        $mageShipment->setTotalQty($totalQty);
        $carrierMethod = $this->_helper->getCarrierByTemandoId($quote->getCarrierId())
                ->getCompanyName() . ' - ' . $quote->getDeliveryMethod();
        $this->_shipmentTrack
            ->setCarrierCode(\Temando\Temando\Model\Shipping\Carrier\Temando::getCode())
            ->setTitle($carrierMethod)
            ->setNumber($resultXml->requestId)
            ->setConsignmentNumber($resultXml->consignmentNumber)
            ->setTrackUrl($quote->getCarrier()->getWebsite());
        $mageShipment->addTrack($this->_shipmentTrack)->register();
        $mageShipment
            ->setIsTemando(true)
            ->setLabelDocument($resultXml->labelDocument)
            ->setLabelDocumentType($resultXml->labelDocumentType)
            ->setConsignmentDocument($resultXml->consignmentDocument)
            ->setConsignmentDocumentType($resultXml->consignmentDocumentType)
            ->setCommercialInvoiceDocument($resultXml->commercialInvoiceDocument)
            ->setCommercialInvoiceDocumentType($resultXml->commercialInvoiceDocumentType)
            ->setShippingCost($quote->getTotalPrice())
            ->setWarehouseId($this->getOriginId())
            ->setFulfilledBy('admin');
        $mageShipment->getOrder()->setIsInProcess(true)->setCustomerNoteNotify(true);
        $mageShipment->save();

        //save shipment items only after processing Magento shipment
        foreach ($processedItems as $processedItem) {
            $processedItem->save();
        }
    }

    /**
     * Get Delivery Options - convert object data into values for SOAP Request array.
     *
     * @return array
     */
    public function getDeliveryOptions()
    {
        $deliveryOptions = array();
        if ($this->getDestinationAuthorityToLeave()) {
            $deliveryOptions['unattended_delivery'] = 1;
        }
        return $deliveryOptions;
    }

    /**
     * Get Pickslip Filename.
     *
     * @return string
     */
    public function getPickslipFilename()
    {
        return "pickslip-shipment-".$this->getId().'.pdf';
    }
}
