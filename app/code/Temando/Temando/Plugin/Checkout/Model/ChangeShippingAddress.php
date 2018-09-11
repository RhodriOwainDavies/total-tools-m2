<?php

/**
 * Change Shipping Address.
 *
 * @category Temando
 * @package  Temando_Temando
 */

namespace Temando\Temando\Plugin\Checkout\Model;

class ChangeShippingAddress extends \Magento\Checkout\Model\GuestShippingInformationManagement
{
    /**
     * Quote ID Mask Factory.
     *
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * Shipping Information Management.
     *
     * @var \Magento\Checkout\Api\ShippingInformationManagementInterface
     */
    protected $shippingInformationManagement;

    /**
     * Checkout Session.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     *
     * @codeCoverageIgnore
     */
    protected $_checkoutSession;

    /**
     * Origin Collection.
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
        \Magento\Sales\Api\Data\OrderAddressInterface $orderAddressInterface,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement,
        \Temando\Temando\Model\Origin $origin
    ) {
        $this->_originCollection = $origin;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderAddressInterface = $orderAddressInterface;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->shippingInformationManagement = $shippingInformationManagement;
    }

    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\GuestShippingInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {

        if ($addressInformation->getShippingMethodCode()=="storepickup") {
            $storepickup_session = $this->_checkoutSession->getData('storepickup_session');
            $datashipping = array();
            $storeId = $storepickup_session['origin_id'];
            $store = $this->_originCollection->load($storeId);
            $datashipping['firstname'] = __('Store');
            $datashipping['lastname'] = $store->getData('name');
            $datashipping['street'][0] = $store->getData('street');
            $datashipping['city'] = $store->getCity();
            $datashipping['region'] = $store->getState();
            $datashipping['postcode'] = $store->getData('postcode');
            $datashipping['country_id'] = $store->getData('country');
            $datashipping['company'] = $store->getData('company_name');
            if ($store->getFax()) {
                $datashipping['fax'] = $store->getFax();
            } else {
                unset($datashipping['fax']);
            }

            if ($store->getPhone()) {
                $datashipping['telephone'] = $store->getContactPhone1();
            } else {
                unset($datashipping['telephone']);
            }

            $datashipping['save_in_address_book'] = 1;
            $addressInformation->getShippingAddress()->addData($datashipping);
        }
        //var_dump($addressInformation->getShippingAddress()->getData());
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->shippingInformationManagement->saveAddressInformation(
            $quoteIdMask->getQuoteId(),
            $addressInformation
        );
    }
}
