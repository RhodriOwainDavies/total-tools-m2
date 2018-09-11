<?php
namespace Temando\Temando\Observer;

use Magento\Framework\Event\ObserverInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class SaveStorepickupDecription implements ObserverInterface
{
    /**
     * Checkout Session.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     *
     * @codeCoverageIgnore
     */
    protected $_checkoutSession;

    /**
     * Origin Collection
     *
     * @var \Temando\Temando\Model\Origin
     */
    protected $_originCollection;

    /**
     * Order Address Interface.
     *
     * @var \Magento\Sales\Api\Data\OrderAddressInterface
     */
    protected $_orderAddressInterface;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Temando\Temando\Model\Origin $originCollection,
        \Magento\Sales\Api\Data\OrderAddressInterface $orderAddressInterface,
        \Temando\Temando\Model\Origin $origin
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_originCollection = $origin;
        $this->_orderAddressInterface = $orderAddressInterface;
    }

    /**
     * Execute.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();
            
            /**
             * If order contains a virtual item, then do not create pickup
             */
            $items = $order->getAllItems();
            foreach ($items as $item) {
                if ($item->getIsVirtual()) {
                    return;
                }
            }
            if ($order->getShippingMethod(true)->getCarrierCode() == "storepickup") {
                if ($this->_checkoutSession->getData('storepickup_session')) {
                    //$new = $order->getShippingDescription();
                    $storepickup_session = $this->_checkoutSession->getData('storepickup_session', true);
                    $datashipping = array();
                    $storeId = $storepickup_session['origin_id'];
                    $store = $this->_originCollection->load($storeId);
                    //set shipping desciption
                    if (isset($storepickup_session['shipping_date']) && isset($storepickup_session['shipping_time'])) {
                        $collectedDate = date(
                            'Y-m-d H:i:s',
                            strtotime(
                                $storepickup_session['shipping_date']
                                . ' '
                                . $storepickup_session['shipping_time']
                            )
                        );
                    } else {
                        $collectedDate = '';
                    }
                    $order->setShippingDescription($collectedDate);
                    //set shipping address
                    $datashipping['firstname'] = __('Store');
                    // save store Id in middlename for pickupstore
                    $datashipping['middlename'] = $storeId;
                    $datashipping['lastname'] = $store->getData('name');
                    $datashipping['street'][0] = $store->getData('street');
                    $datashipping['city'] = $store->getCity();
                    $datashipping['region'] = $store->getRegion();
                    $datashipping['postcode'] = $store->getData('postcode');
                    $datashipping['country_id'] = $store->getData('country');
                    $datashipping['company'] = $store->getData('company_name');
                    if ($store->getContactFax()) {
                        $datashipping['fax'] = $store->getContactFax();
                    } else {
                        unset($datashipping['fax']);
                    }
                    if ($store->getContactPhone1()) {
                        $datashipping['telephone'] = $store->getContactPhone1();
                    } else {
                        unset($datashipping['telephone']);
                    }
                    $datashipping['save_in_address_book'] = 0;
                    $order->getShippingAddress()->addData($datashipping);
                }
            }
        } catch (Exception $e) {
        }
    }
}
