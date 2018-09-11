<?php

namespace Temando\Temando\Observer;

use Magento\Framework\Event\ObserverInterface;
use Symfony\Component\Config\Definition\Exception\Exception;

class AdminhtmlSaveStorepickupDescription implements ObserverInterface
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
     * Origin Factory.
     *
     * @var \Temando\Temando\Model\OriginFactory
     */
    protected $_originCollection;

    /**
     * Order Address Interface.
     *
     * @var \Magento\Sales\Api\Data\OrderAddressInterface
     */
    protected $_orderAddressInterface;

    /**
     * Storepickup Helper.
     *
     * @var \Magestore\Storepickup\Helper\Data
     */
    protected $_storepickupHelper;

    /**
     * Email Helper.
     *
     * @var \Magestore\Storepickup\Helper\Email
     */
    protected $_storepickupHelperEmail;

    /**
     * AdminhtmlSaveStorepickupDecription constructor.
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Temando\Temando\Model\OriginFactory $storeCollection
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $orderAddressInterface
     * @param \Magestore\Storepickup\Helper\Data $storepickupHelper
     * @param \Magestore\Storepickup\Helper\Email $storepickupHelperEmail
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Temando\Temando\Model\OriginFactory $originCollection,
        \Magento\Sales\Api\Data\OrderAddressInterface $orderAddressInterface,
        \Magestore\Storepickup\Helper\Data $storepickupHelper,
        \Magestore\Storepickup\Helper\Email $storepickupHelperEmail
    ) {
        $this->_backendSession = $backendSession;
        $this->_originCollection = $originCollection;
        $this->_orderAddressInterface = $orderAddressInterface;
        $this->_storepickupHelper = $storepickupHelper;
        $this->_storepickupHelperEmail = $storepickupHelperEmail;
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
            if ($order->getShippingMethod(true)->getCarrierCode()=="storepickup") {
                if ($this->_backendSession->getData('storepickup')) {
                    $new = $order->getShippingDescription();
                    $storepickup_session = $this->_backendSession->getData('storepickup', true);
                    $datashipping = array();
                    $storeId = $storepickup_session['store_id'];
                    $collectionstore = $this->_originCollection->create();
                    $store = $collectionstore->load($storeId, 'origin_id');
                    //set Shipping Description
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

                    //set Shipping Address
                    $datashipping['firstname'] = $store->getData('name');
                    $datashipping['middlename'] = $store->getId();
                    $datashipping['middle_name'] = $store->getId();
                    $datashipping['lastname'] = '';

                    $datashipping['street'][0] = $store->getData('street');
                    $datashipping['city'] = $store->getCity();
                    $datashipping['region'] = $store->getRegion();
                    $datashipping['postcode'] = $store->getData('postcode');
                    $datashipping['country_id'] = $store->getData('country');
                    $datashipping['company'] = '';
                    if ($store->getFax()) {
                        $datashipping['fax'] = $store->getFax();
                    } else {
                        unset($datashipping['fax']);
                    }

                    if ($store->getPhone()) {
                        $datashipping['telephone'] = $store->getPhone();
                    } else {
                        unset($datashipping['telephone']);
                    }

                    $datashipping['save_in_address_book'] = 0;

                    $order->getShippingAddress()->addData($datashipping);
                    //$order->sendNewOrderEmail();
                    //$this->_storepickupHelperEmail->sendNoticeEmailToStoreOwner($order,$store);
                    //$this->_storepickupHelperEmail->sendNoticeEmailToAdmin($order,$store);
                    //$this->_emailHelper->sendOrderAllocationEmailToMerchant($order, $origin);
                }
            }
        } catch (Exception $e) {
        }
    }
}
