<?php

namespace Temando\Temando\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderCommitAfter implements ObserverInterface
{
    /**
     * Magestore Pickup shipping method prefix.
     */
    const PICKUP_SHIPPING_METHOD_PREFIX = 'storepickup_';

    /**
     * Temando Shipment shipping method prefix.
     */
    const TEMANDO_SHIPPING_METHOD_PREFIX = 'temando_';

    /**
     * Quote Repository.
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $_quoteRepository;

    /**
     * Temando shipment.
     *
     * @var \Temando\Temando\Model\Shipment
     */
    protected $_shipment;

    /**
     * Temando pickup.
     *
     * @var \Temando\Temando\Model\Pickup
     */
    protected $_pickup;

    /**
     * Temando box.
     *
     * @var \Temando\Temando\Model\Box
     */
    protected $_box;

    /**
     * Catalog Product.
     *
     * @var \Magento\Catalog\Product
     */
    protected $_product;

    /**
     * Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * Email Helper.
     *
     * @var \Temando\Temando\Helper\Email
     */
    protected $_emailHelper;

    /**
     * Checkout Session.
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Scope Interface.
     *
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeConfig;

    /**
     * Resource Connection.
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * Logger Interface.
     *
     * @var \Temando\Temando\Logger\General\Logger
     */
    protected $_logger;

    /**
     * Origin Collection.
     *
     * @var \Temando\Temando\Model\Resource\Origin\Collection
     */
    protected $_originCollection;

    /**
     * Origin Factory.
     *
     * @var \Temando\Temando\Model\OriginFactory
     */
    protected $_originFactory;

    /**
     * Temando Quote.
     *
     * @var \Temando\Temando\Model\Quote
     */
    protected $_quote;

    /**
     * Rule Type (disused as part of TTMA-274).
     *
     * @var \Temando\Temando\Model\System\Config\Source\Rule\Type
     */
    protected $_type;

    /**
     * Rule Collection.
     *
     * @var \Temando\Temando\Model\ResourceModel\Rule\Collection
     */
    protected $_ruleCollection;

    /**
     * Shipment Factory.
     *
     * @var \Temando\Temando\Model\ShipmentFactory
     */
    protected $_shipmentFactory;

    /**
     * Pickup Factory.
     *
     * @var \Temando\Temando\Model\PickupFactory
     */
    protected $_pickupFactory;

    /**
     * Store Manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * SalesOrderCommitAfter constructor.
     *
     * @param \Temando\Temando\Model\Shipment $shipment
     * @param \Temando\Temando\Model\Pickup $pickup
     * @param \Temando\Temando\Model\Box $box
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Temando\Temando\Helper\Email $emailHelper
     * @param \Temando\Temando\Model\Resource\Origin\Collection $originCollection
     * @param \Temando\Temando\Model\Quote $quote
     * @param \Temando\Temando\Model\System\Config\Source\Rule\Type $type
     * @param \Temando\Temando\Model\ResourceModel\Rule\Collection $ruleCollection
     * @param \Temando\Temando\Model\ShipmentFactory $shipmentFactory
     * @param \Temando\Temando\Model\PickupFactory $pickupFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Temando\Temando\Logger\General\Logger $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */

    public function __construct(
        \Temando\Temando\Model\Shipment $shipment,
        \Temando\Temando\Model\Pickup $pickup,
        \Temando\Temando\Model\Box $box,
        \Temando\Temando\Helper\Data $helper,
        \Temando\Temando\Helper\Email $emailHelper,
        \Temando\Temando\Model\Resource\Origin\Collection $originCollection,
        \Temando\Temando\Model\OriginFactory $originFactory,
        \Temando\Temando\Model\Quote $quote,
        \Temando\Temando\Model\System\Config\Source\Rule\Type $type,
        \Temando\Temando\Model\ResourceModel\Rule\Collection $ruleCollection,
        \Temando\Temando\Model\ShipmentFactory $shipmentFactory,
        \Temando\Temando\Model\PickupFactory $pickupFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product $product,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Temando\Temando\Logger\General\Logger $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_shipment = $shipment;
        $this->_pickup = $pickup;
        $this->_box = $box;
        $this->_helper = $helper;
        $this->_emailHelper = $emailHelper;
        $this->_originCollection = $originCollection;
        $this->_originFactory = $originFactory;
        $this->_quote = $quote;
        $this->_type = $type;
        $this->_ruleCollection = $ruleCollection;
        $this->_shipmentFactory = $shipmentFactory;
        $this->_pickupFactory = $pickupFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_product = $product;
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteRepository = $quoteRepository;
        $this->_resourceConnection = $resourceConnection;
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
    }

    /**
     * Execute.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return \Temando\Temando\Model\Shipment
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        
        /* If order contains a virtual item, then do not create shipment or pickup */
        $items = $order->getAllItems();
        foreach ($items as $item) {
            if ($item->getIsVirtual()) {
                return;
            }
        }
        
        $origin = null;

        if (substr($order->getShippingMethod(), 0, strlen(self::TEMANDO_SHIPPING_METHOD_PREFIX))
            === self::TEMANDO_SHIPPING_METHOD_PREFIX
        ) {
            $shipment = $this->_shipmentFactory->create()->load($order->getId(), 'order_id');
            if ($shipment->getId()) {
                return;
            }
            try {
                $this->createTemandoShipment($observer);
            } catch (\Exception $e) {
                $this->_logger->debug("error when creating temando shipment order Id =".$order->getId());
                $this->_logger->debug($e->getMessage());
            }
            $origin = $this->_shipment->getOrigin();
        } elseif (substr($order->getShippingMethod(), 0, strlen(self::PICKUP_SHIPPING_METHOD_PREFIX))
            === self::PICKUP_SHIPPING_METHOD_PREFIX
        ) {
            $pickup = $this->_pickupFactory->create()->load($order->getId(), 'order_id');
            if ($pickup->getId()) {
                return;
            }

            $this->createTemandoPickup($observer);
            $origin = $this->_pickup->getOrigin();
        } else {
            return false;
        }
        try {
            $this->_emailHelper->sendOrderAllocationEmailToMerchant($order, $origin);
            $this->_checkoutSession->setTemandoRequestString(null);
        } catch (\Exception $e) {
            $this->_logger->debug("error when sending order, setting checkout session order Id =".$order->getId());
            $this->_logger->debug($e->getMessage());
        }
        return;
    }

    /**
     * Create Temando Pickup
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return \Temando\Temando\Model\Pickup
     */
    public function createTemandoPickup(\Magento\Framework\Event\Observer $observer)
    {
        $deprecateSkus = array();
        $order = $observer->getEvent()->getOrder();
        $billingAddress = $order->getBillingAddress();

        $shippingAddress = $order->getShippingAddress();
        // middlename is actually store id in pickupstore
        $storeId = $shippingAddress->getMiddleName();

        $this->_pickup->setData('order_id', $order->getId());
        $this->_pickup->setData('order_increment_id', $order->getIncrementId());

        $this->_pickup->setData('origin_id', $storeId);
        $this->_pickup->setData('customer_selected_origin', $storeId);

        $pickupStatus = \Temando\Temando\Model\System\Config\Source\Pickup\Status::PENDING;

        $origin = $this->_originFactory->create()->load($storeId);

        $exclusiveOD = $this->_helper->orderContainsExclusively($order, "OD");

        $orderSkus = $this->_helper->getOrderSkus($order);
        foreach ($orderSkus as $sku => $details) {
            if ($details['stock_availability_code'] == "OD") {
                $hasStock = $origin->hasStock(array($sku => $details['qty']));
                if ($hasStock) {
                    //deprecate
                    $deprecateSkus[$sku] = $details['qty'];
                } elseif ($exclusiveOD) {
                    $pickupStatus = \Temando\Temando\Model\System\Config\Source\Pickup\Status::BACK_ORDER;
                }
            } else {
                //deprecate - stock check already assumed in getOriginByInventory
                $deprecateSkus[$sku] = $details['qty'];
            }
        }

        $this->_pickup->setData('status', $pickupStatus);

        $this->_pickup->setData('billing_contact_name', $billingAddress->getName());
        $this->_pickup->setData('billing_company_name', $billingAddress->getCompany());
        $streetAddress = $billingAddress->getStreetLine(1);
        if ($billingAddress->getStreetLine(2)) {
            $streetAddress .= ", " . $billingAddress->getStreetLine(2);
        }
        $this->_pickup->setData('billing_street', $streetAddress);
        $this->_pickup->setData('billing_city', $billingAddress->getCity());
        $this->_pickup->setData('billing_postcode', $billingAddress->getPostcode());
        $this->_pickup->setData('billing_region', $billingAddress->getRegion());
        $this->_pickup->setData('billing_country', $billingAddress->getCountryId());
        $this->_pickup->setData('billing_phone', $billingAddress->getTelephone());
        $this->_pickup->setData('billing_email', $order->getCustomerEmail());
        try {
            $this->_pickup->save();
        } catch (\Exception $e) {
            $this->_logger->debug(__('Failed to save pickup') . ' ' . $e->getMessage());
        }

        $originErpId = $this->_pickup->getOrigin()->getErpId();

        if ((count($deprecateSkus)) && ($originErpId)) {
            $this->deprecateSkus($this->_pickup->getOrigin()->getErpId(), $deprecateSkus);
        }

        $this->clearSessionData();

        return $this->_pickup;
    }


    /**
     * Create Temando Shipment object
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return \Temando\Temando\Model\Shipment
     */
    public function createTemandoShipment(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $shippingAddress = $order->getShippingAddress();
        $deprecateSkus = array();

        $shippingMethod = $order->getShippingMethod();
        $titleOptions = $this->_type->toOptionArray();
        $titles = array();
        foreach ($titleOptions as $value => $optionTitle) {
            $titles[$value] = $optionTitle['label'];
        }

        $quote = $this->_quoteRepository->get($order->getQuoteId());
        $quoteShippingAddress = $quote->getShippingAddress();

        $origin = $this->_originCollection->getOriginByInventory(
            $order->getAllVisibleItems(),
            $quoteShippingAddress->getPostcode()
        );
        //$origin = $this->_originCollection->getOriginByPostcode($quoteShippingAddress->getPostcode());
        $this->_shipment->setData('order_id', $order->getId());
        $this->_shipment->setData('order_increment_id', $order->getIncrementId());
        $this->_shipment->setData('origin_id', $origin->getId());

        $orderSkus = $this->_helper->getOrderSkus($order);
        $shipmentStatus = \Temando\Temando\Model\System\Config\Source\Shipment\Status::PENDING;

        $exclusiveOD = $this->_helper->orderContainsExclusively($order, "OD");
        foreach ($orderSkus as $sku => $details) {
            if ($details['stock_availability_code'] == "OD") {
                $hasStock = $origin->hasStock(array($sku => $details['qty']));
                if ($hasStock) {
                    $deprecateSkus[$sku] = $details['qty'];
                } elseif ($exclusiveOD) {
                    $shipmentStatus = \Temando\Temando\Model\System\Config\Source\Shipment\Status::BACK_ORDER;
                }
            } else {
                $deprecateSkus[$sku] = $details['qty'];
            }
        }
        $this->_shipment->setData('status', $shipmentStatus);
        $this->_shipment->setData('destination_contact_name', $shippingAddress->getName());
        $this->_shipment->setData('destination_company_name', $shippingAddress->getCompany());
        $streetAddress = $shippingAddress->getStreetLine(1);
        if ($shippingAddress->getStreetLine(2)) {
            $streetAddress .= ", " . $shippingAddress->getStreetLine(2);
        }
        $this->_shipment->setData('destination_street', $streetAddress);
        $this->_shipment->setData('destination_city', $shippingAddress->getCity());
        $this->_shipment->setData('destination_postcode', $shippingAddress->getPostcode());
        $this->_shipment->setData('destination_region', $shippingAddress->getRegion());
        $this->_shipment->setData('destination_country', $shippingAddress->getCountryId());
        $this->_shipment->setData('destination_phone', $shippingAddress->getTelephone());
        $this->_shipment->setData('destination_email', $order->getCustomerEmail());

        //set customer selected quote data
        $temandoQuoteData = explode('_', $shippingMethod);
        $temandoQuoteId = $temandoQuoteData[2];

        $quoteDescription = '-';
        switch ($temandoQuoteData[2]) {
            default:
                $temandoQuote = $this->_quote->load($temandoQuoteId);
                $carrier = $this->_helper->getCarrierByTemandoId($temandoQuote->getCarrierId());
                $quoteDescription = $carrier->getCompanyName() . ' - ' . $temandoQuote->getDeliveryMethod();
                $totalPrice = $temandoQuote->getTotalPrice();
                break;
        }

        $this->_shipment->setData('customer_selected_quote_id', $temandoQuoteId);
        $this->_shipment->setData('customer_selected_options', $shippingMethod);
        $this->_shipment->setData('customer_selected_quote_description', $quoteDescription);
        $this->_shipment->setData('admin_selected_quote_id', $temandoQuoteId);
        $this->_shipment->setData('anticipated_cost', $totalPrice);

        if ($shippingAddress->getIsBusinessAddress()) {
            $this->_shipment->setData(
                'destination_type',
                \Temando\Temando\Model\System\Config\Source\Origin\Type::BUSINESS
            );
        } else {
            $this->_shipment->setData(
                'destination_type',
                \Temando\Temando\Model\System\Config\Source\Origin\Type::RESIDENTIAL
            );
        }

        if ($shippingAddress->getAuthorityToLeave()) {
            $this->_shipment->setData('destination_authority_to_leave', 1);
        } else {
            $this->_shipment->setData('destination_authority_to_leave', 0);
        }

        try {
            $this->_shipment->save();
        } catch (\Exception $e) {
            $this->_logger->debug(__('Failed to save shipment') . ' ' . $e->getMessage());
        }
        //register the quotes with the shipment
        //$this->registerQuotes($order);
        try {
            $this->_shipment->saveAllItems();
        } catch (\Exception $e) {
            $this->_logger->debug(__('Failed to save shipment items') . ' ' . $e->getMessage());
        }


        //how many boxes required?
        $this->saveBoxes($order);

        if ((count($deprecateSkus)) && ($origin->getErpId())) {
            $this->deprecateSkus($origin->getErpId(), $deprecateSkus);
        }
        //$this->_shipment->fetchQuotes();//or update existing quotes to use this shipment

        $this->clearSessionData();

        return $this->_shipment;
    }

    /**
     * Clear checkout session values after customer checks out
     */
    protected function clearSessionData()
    {
        $this->_checkoutSession
            ->unsetData('temando_consolidated_packaging')
            ->unsetData('selected_delivery_options')
            ->unsetData('destination_type');
    }

    /**
     * Register the quotes to the created shipment
     *
     * This is in the observer and has the order object because
     * for multishipping the order hasn't been saved yet and using
     * $shipment->getOrder() would not work in this case
     *
     * @param \Magento\Sales\Model\Order $order
     * @param \Temando\Temando\Model\Shipment $shipment
     */
    public function registerQuotes($order)
    {
        return true;
    }


    /*
     * Calulate and Save Boxes for this Order
     *
     * @param \Magento\Sales\Model\Order $order
     * @return \Magento\Sales\Model\Order $order
     */
    public function saveBoxes($order)
    {
        $currencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        $boxesRequired = $this->_helper->calculateBoxes($order->getAllVisibleItems(), $currencyCode);

        foreach ($boxesRequired as $boxIndex => $boxInfo) {
            $boxLength  = $boxInfo['length'] ? $boxInfo['length'] : $this->_scopeConfig->getValue(
                'temando/defaults/length',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $boxWidth   = $boxInfo['width'] ? $boxInfo['width'] : $this->_scopeConfig->getValue(
                'temando/defaults/width',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $boxHeight  = $boxInfo['height'] ? $boxInfo['height'] : $this->_scopeConfig->getValue(
                'temando/defaults/height',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $boxWeight  = $boxInfo['weight'] ? $boxInfo['weight'] : $this->_scopeConfig->getValue(
                'temando/defaults/height',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $boxArticles = array();

            $this->_box
                ->setShipmentId($this->_shipment->getId())
                ->setComment($boxInfo['comment'])
                ->setQty($boxInfo['qty'])//->setQty($qty)
                ->setValue($boxInfo['value'])
                ->setLength($boxLength)
                ->setWidth($boxWidth)
                ->setHeight($boxHeight)
                ->setMeasureUnit(
                    $this->_scopeConfig->getValue(
                        'temando/units/measure',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                )->setWeight($boxWeight)
                ->setWeightUnit(
                    $this->_scopeConfig->getValue(
                        'temando/units/weight',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                )->setFragile($boxInfo['fragile'])
                ->setDangerous($boxInfo['dangerous'])
                ->setArticles(json_encode($boxInfo['comment']))
                ->save();
            //$this->_box->cleanModelCache()->clearInstance();
            $this->_box->setId(null);
        }
        return $order;
    }

    /**
     * Deprecate Skus in temando_origin_inventory table
     *
     * @param $originErpId
     * @param $order
     */
    public function deprecateSkus($originErpId, $skus)
    {
        $connection = $this->_resourceConnection->getConnection();
        $tableName = $this->_resourceConnection->getTableName('temando_origin_inventory');
        foreach ($skus as $sku => $qty) {
            $updateQuery = "UPDATE " . $tableName . " SET units=(units-" . $qty .
                ")  WHERE erp_id='".$originErpId."' AND sku='". addslashes($sku)."'";

            $connection->query($updateQuery);
        }
    }
}
