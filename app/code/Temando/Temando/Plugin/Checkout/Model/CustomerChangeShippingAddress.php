<?php

/**
 * Change Shipping Address.
 *
 * @category Temando
 * @package  Temando_Temando
 */

namespace Temando\Temando\Plugin\Checkout\Model;

class CustomerChangeShippingAddress extends \Magento\Checkout\Model\ShippingInformationManagement
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
     * Origin Collection.
     *
     * @var \Temando\Temando\Model\Origin
     */
    protected $_originCollection;
    
    /**
     * Customer Session.
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\QuoteAddressValidator $addressValidator,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Temando\Temando\Model\Origin $origin,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_originCollection = $origin;
        $this->_checkoutSession = $checkoutSession;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->cartTotalsRepository = $cartTotalsRepository;
        $this->quoteRepository = $quoteRepository;
        $this->addressValidator = $addressValidator;
        $this->logger = $logger;
        $this->addressRepository = $addressRepository;
        $this->scopeConfig = $scopeConfig;
        $this->totalsCollector = $totalsCollector;
        $this->_customerSession = $customerSession;
    }

    public function aroundSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        \Closure $proceed,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $address = $addressInformation->getShippingAddress();
        $carrierCode = $addressInformation->getShippingCarrierCode();
        $methodCode = $addressInformation->getShippingMethodCode();

        $quote = $this->quoteRepository->getActive($cartId);
        $this->validateQuote($quote);

        $saveInAddressBook = $address->getSaveInAddressBook() ? 1 : 0;
        $sameAsBilling = $address->getSameAsBilling() ? 1 : 0;
        $customerAddressId = $address->getCustomerAddressId();
        $this->addressValidator->validate($address);
        $quote->setShippingAddress($address);
        $address = $quote->getShippingAddress();
        
        if ($customerAddressId) {
            $addressData = $this->addressRepository->getById($customerAddressId);
            $address = $quote->getShippingAddress()->importCustomerAddressData($addressData);
        }
        $billingAddress = $addressInformation->getBillingAddress();
        if ($billingAddress) {
            $quote->setBillingAddress($billingAddress);
        }

        $address->setSaveInAddressBook($saveInAddressBook);
        $address->setSameAsBilling($sameAsBilling);
        $address->setCollectShippingRates(true);

        if (!$address->getCountryId()) {
          //  throw new StateException(__('Shipping address is not set'));
        }

        $address->setShippingMethod($carrierCode . '_' . $methodCode);

        try {
            $this->totalsCollector->collectAddressTotals($quote, $address);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        //    throw new InputException(__('Unable to save address. Please, check input data.'));
        }

        if (!$address->getShippingRateByCode($address->getShippingMethod())) {
         //   throw new NoSuchEntityException(
           //     __('Carrier with such method not found: %1, %2', $carrierCode, $methodCode)
           // );
        }

        if (!$quote->validateMinimumAmount($quote->getIsMultiShipping())) {
         //   throw new InputException($this->scopeConfig->getValue(
           //     'sales/minimum_order/error_message',
            //    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
           //     $quote->getStoreId()
           // ));
        }
        $customerSession = $this->_customerSession;
                
        if ($addressInformation->getShippingMethodCode()=="storepickup" && $customerSession->isLoggedIn()) {
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

            $datashipping['save_in_address_book'] = 0;
            $address->setSameAsBilling(0);
            $address->addData($datashipping);
        }
        try {
            $address->save();
            $quote->collectTotals();
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            //throw new InputException(__('Unable to save shipping information. Please, check input data.'));
        }

        $paymentDetails = $this->paymentDetailsFactory->create();
        $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($cartId));
        $paymentDetails->setTotals($this->cartTotalsRepository->get($cartId));
        return $paymentDetails;
    }
}
