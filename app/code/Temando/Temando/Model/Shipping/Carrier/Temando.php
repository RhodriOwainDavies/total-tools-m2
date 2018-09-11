<?php

namespace Temando\Temando\Model\Shipping\Carrier;

use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;

/**
 * Temando Carrier Class
 */
class Temando extends AbstractCarrier implements CarrierInterface
{

    /**
     * Error Constants
     */
    const ERR_INVALID_COUNTRY = 'Sorry, shipping to selected country is not available.';
    const ERR_INVALID_DEST    = 'Please enter a delivery address to view available shipping methods';
    const ERR_NO_METHODS      = 'No shipping methods available';
    const ERR_INTERNATIONAL   = 'International delivery is not available at this time.';
    const ERR_NO_ORIGIN       = 'Unable to fullfil the order at this time due to missing origin data.';

    const CACHE_LIFETIME = 3600; // 1 Hour
    const CACHE_TAG = 'temando';

    /**
     * Error Map
     *
     * @var array
     */
    protected $_errors_map = array();

    /**
     * Carrier code
     */
    const CARRIER_CODE = 'temando';
    
    /**
     * This carrier class code.
     *
     * @var string
     */
    protected $_code = 'temando';

    /**
     * Carrier title.
     *
     * @var string
     */
    const CARRIER_TITLE = 'Temando';

    /**
     * Rates result.
     *
     * @var array|null
     */
    protected $_rates;

    /**
     * Rate Result Factory.
     *
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * Origin.
     *
     * @var \Temando\Temando\Model\Origin
     */
    protected $_origin;

    /**
     * Origin Collection.
     *
     * @var \Temando\Temando\Model\Resource\Origin
     */
    protected $_originCollection;

    /**
     * Checkout Session.
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * API Request.
     *
     * @var \Temando\Temando\Model\Api\Request
     */
    protected $_apiRequest;

    /**
     * Temando Carriers.
     *
     * @var \Temando\Temando\Model\Carrier
     */
    protected $_carrier;

    /**
     * Cache.
     *
     * @var \Temando\Temando\Model\Cache\Type
     */
    protected $_cache;

    /**
     * Quote Collection.
     *
     * @var \Temando\Temando\Model\ResourceModel\Quote\Collection
     */
    protected $_quoteCollection;

    /**
     * Rules Engine.
     *
     * @var \Temando\Temando\Model\Hybrid
     */
    protected $_hybrid;

    /**
     * Store Manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Backend Session Quote.
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_backendSession;

    protected $_appState;
    /**
     * Temando constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Temando\Temando\Model\Carrier $carrier
     * @param \Temando\Temando\Model\Origin $origin
     * @param \Temando\Temando\Model\Resource\Origin\Collection $originCollection
     * @param \Temando\Temando\Model\Api\Request $apiRequest
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Temando\Temando\Model\Cache\Type $cache
     * @param \Temando\Temando\Model\ResourceModel\Quote\Collection $quoteCollection
     * @param \Temando\Temando\Model\Hybrid $hybrid
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Backend\Model\Session\Quote $backendSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Cart $cart,
        \Temando\Temando\Model\Carrier $carrier,
        \Temando\Temando\Model\Origin $origin,
        \Temando\Temando\Model\Resource\Origin\Collection $originCollection,
        \Temando\Temando\Model\Api\Request $apiRequest,
        \Temando\Temando\Helper\Data $helper,
        \Temando\Temando\Model\Cache\Type $cache,
        \Temando\Temando\Model\ResourceModel\Quote\Collection $quoteCollection,
        \Temando\Temando\Model\Hybrid $hybrid,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Session\Quote $backendSession,
        \Magento\Framework\App\State $appState,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_helper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->_origin = $origin;
        $this->_originCollection = $originCollection;
        $this->_apiRequest = $apiRequest;
        $this->_carrier = $carrier;
        $this->_cache = $cache;
        $this->_quoteCollection = $quoteCollection;
        $this->_hybrid = $hybrid;
        $this->_backendSession = $backendSession;
        $this->_appState = $appState;

        parent::__construct(
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $data
        );
        $this->setErrorsMap();
    }

    /**
     * Returns available shipping methods for current request based on pricing method
     * specified in Temando Settings.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
     *
     * @return \Magento\Shipping\Model\Rate\Result
     */
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $rateRequest)
    {
        /**
         * Rate Result.
         *
         * @var \Magento\Shipping\Model\Rate\Result $result
         */
        $result = $this->_rateResultFactory->create();

        if (!$rateRequest->getDestCountryId()) {
            $this->_logger->debug(__('Temando requires a country id, RateRequest doesn\'t have a country id'));
            if (!$this->_scopeConfig->getValue(
                'carriers/temando/showmethod',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )) {
                return;
            }
            return $this->_getErrorMethod(__(self::ERR_INVALID_DEST));
        }

        if (!$rateRequest->getDestPostcode()) {
            $this->_logger->debug(__('Temando requires a postcode, RateRequest doesn\'t have a postcode'));
            if (!$this->_scopeConfig->getValue(
                'carriers/temando/showmethod',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )) {
                return;
            }
            return $this->_getErrorMethod(__(self::ERR_INVALID_DEST));
        }

        if (!$rateRequest->getDestCity()) {
            $this->_logger->debug(__('Temando requires a city, RateRequest doesn\'t have a city'));
            if (!$this->_scopeConfig->getValue(
                'carriers/temando/showmethod',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )) {
                return;
            }
            return $this->_getErrorMethod(__(self::ERR_INVALID_DEST));
        }

        $salesQuote = $this->_checkoutSession->getQuote();
        if (!$salesQuote->getId() && $this->_helper->isAdmin($this->_appState)) {
            $salesQuote = $this->_backendSession->getQuote();
        }
//        if ($this->getIsProductPage()) {
//            $salesQuote = Mage::helper('temando')->getDummySalesQuoteFromRequest($rateRequest);
//        }
        $salesQuoteId = $salesQuote->getId();

        $this->_origin = $this->_originCollection->getOriginByInventory(
            $rateRequest->getAllItems(),
            $rateRequest->getDestPostcode()
        );

        if (!$this->_origin) {
            //did not find any origins which could serve current request
            if (!$this->_scopeConfig->getValue(
                'carriers/temando/showmethod',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )) {
                return;
            }
            return $this->_getErrorMethod(__(self::ERR_NO_ORIGIN));
        }

        //init rule engine, preload applicable rules
        $ruleEngine = $this->_hybrid->loadRules($salesQuote);
        if (!$ruleEngine->hasValidRules()) {
            //no valid rules => no available shipping methods
//            if (!Mage::getStoreConfig('carriers/temando/showmethod')) {
//                return;
//            }
            return $this->_getErrorMethod(self::ERR_NO_METHODS);
        }

        //get available quotes from the API for current request
        $quotes = array();
        if ($ruleEngine->hasDynamic()) {
            //check if current request same as previous & dynamic rules configured
            $lastRequestString = $this->_checkoutSession->getTemandoRequestString();

            if ($lastRequestString == $this->_createRequestString($rateRequest, $salesQuoteId)) {
                //request is the same as previous and delivery options not changed - load existing quotes from DB
                $collection = $this->_quoteCollection
                    ->addFieldToFilter('magento_quote_id', $salesQuoteId)
                    ->load();
//                if ($this->getDeliverBy() !== false) {
//                    $collection
//                        ->addFieldToFilter(
//                            'eta_to',
//                            array(
//                                'lteq' => Mage::helper('temando')
//                                    ->getMaxEtaForDeliveryByDate($this->getDeliverBy())
//                            )
//                        );
//                }
                $quotes = $collection->getItems();
            } else {
                try {
                    $currencyCode = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
                    $boxesRequired = $this->_helper->calculateBoxes($rateRequest->getAllItems(), $currencyCode);
                    
                    $this->_apiRequest
                        ->setConnectionParams($this->_origin->getTemandoProfile())
                        ->setMagentoQuoteId($salesQuoteId)
                        ->setDestination(
                            $rateRequest->getDestCountryId(),
                            $rateRequest->getDestPostcode(),
                            $rateRequest->getDestCity(),
                            $rateRequest->getDestStreet(),
                            $rateRequest->getDestType()
                        )
                        ->setOrigin($this->_origin->getName(), $this->_origin->getCountry())
                        ->setItems($boxesRequired)
                        ->setReady($this->_helper->getReadyDate())
                        ->setDeliveryOptions($rateRequest->getDeliveryOptions())
                        ->setAllowedCarriers($this->getAllowedMethods());

                    $requestStr = print_r($this->_apiRequest->toRequestArray(), 1);

                    $collectionStr = $this->_cacheLoadShippingOptions($requestStr);

                    if ($collectionStr) {
                        $collection = unserialize($collectionStr);
                    } else {
                        $collection = $this->_apiRequest->getQuotes();

                        if (is_soap_fault($collection)) {
                            $this->_logger->debug('Found SOAP Fault');
                            $errorMessage = $collection->getMessage().' ('.$collection->getCode().')';
                            $this->_logger->debug($errorMessage);
                            return;
                        }
                        $this->_cacheSaveShippingOptions($requestStr, serialize($collection));
                    }

                    if ($collection) {
//                        if ($this->getDeliverBy() !== false) {
//                            $collection
//                                ->addFieldToFilter(
//                                    'eta_to',
//                                    array(
//                                        'lteq' => Mage::helper('temando')
//                                            ->getMaxEtaForDeliveryByDate($this->getDeliverBy())
//                                    )
//                                );
//                        }

                        $quotes = $collection->getItems();
                        $requestString = $this->_createRequestString($rateRequest, $salesQuoteId);
                        $this->_logger->debug(get_class($this) . ' saving request string ('.$requestString.')');
                        $this->_checkoutSession
                            ->setTemandoRequestString(
                                $requestString
                            );
                    } else {
                        $this->_logger->debug(
                            get_class($this) . ' FALSE collection returned from $this->_apiRequest->getQuotes()'
                        );
                    }
                } catch (Exception $e) {
                    $this->_logger->debug(get_class($this).' ' .$e->getMessage());
                    return;
//                    switch($this->_scopeConfig->getValue(
//                      'temando/options/error_process',
//                      \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
//                    ) {
//                        case Temando_Temando_Model_System_Config_Source_Errorprocess::VIEW:
//                            if (!Mage::getStoreConfig('carriers/temando/showmethod')) {
//                                return;
//                            }
//                            return $this->_getErrorMethod($e->getMessage());
//                            break;
//                        case Temando_Temando_Model_System_Config_Source_Errorprocess::CUSTOM:
//                            if (!Mage::getStoreConfig('carriers/temando/showmethod')) {
//                                return;
//                            }
//                            return $this->_getErrorMethod(
//                                Mage::helper('temando')->getConfigData('options/error_process_message')
//                            );
//                            break;
//                        case Temando_Temando_Model_System_Config_Source_Errorprocess::FLAT:
//                            $result->append($this->_getFlatRateMethod());
//                            return $result;
//                            break;
//                    }
                }
            }
        }
        //$this->registerItems($salesQuote);

        $shippingMethods = $ruleEngine->getShippingMethods($quotes);

        //set hasDynamic for checkout shipping methods template
//        if ($ruleEngine->hasDynamic() && !empty($quotes)) {
//            Mage::register('temando_has_dynamic_shipping_methods', true, true);
//        }
        if (empty($shippingMethods)) {
//            if (!Mage::getStoreConfig('carriers/temando/showmethod')) {
//                return;
//            }
//            return $this->_getErrorMethod(Mage::helper('temando')->__(self::ERR_NO_METHODS));
        } else {
            $c = 0;
            foreach ($shippingMethods as $shippingMethod) {
                $method = $this->_rateMethodFactory->create();
                $method->setCarrier($this->_code);
                $method->setCarrierTitle($shippingMethod->getData('carrier_title'));
                $method->setMethod($this->_code."_".$shippingMethod->getRuleQuoteId().'_'.$shippingMethod->getRuleId());
                $method->setMethodTitle($shippingMethod->getData('method_title'));
                $method->setPrice($shippingMethod->getData('price'));
                $method->setCost($shippingMethod->getData('cost'));

                $result->append($method);
            }
        }
        //Mage::register('temando_has_new_shipping_methods', true, true);

        return $result;
    }


    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        $allowed = explode(',', $this->getConfigData('allowed_methods'));
        $arr = [];
        foreach ($allowed as $k) {
            $arr[$k] = $this->getCode('method', $k);
        }
        return $arr;
    }

    /**
     * Returns Temando carrier code
     *
     * @return string
     */
    static public function getCode()
    {
        return self::CARRIER_CODE;
    }

    /**
     * Returns Temando carrier title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_scopeConfig->getValue(
            'carriers/temando/title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        //return self::CARRIER_TITLE;
    }


    /**
     * Returns shipping rate result error method
     *
     * @param string $errorText
     *
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Error
     */
    protected function _getErrorMethod($errorText)
    {
        $error = $this->_rateErrorFactory->create();
        $error->setCarrier(self::getCode());
        $error->setCarrierTitle(self::getTitle());
        if (isset($this->_errors_map[$errorText])) {
            $errorText = $this->_errors_map[$errorText];
        } else {
            $errorText = $this->_scopeConfig->getValue(
                'carriers/temando/specificerrmsg',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        $error->setErrorMessage($errorText);

        return $error;
    }

    /**
     * Sets predefined errors on the error map array
     */
    protected function setErrorsMap()
    {
        $errorsMap = array();
        // add invalid suburb / postcode error
        $invalidPostCode = "The 'destinationCountry', 'destinationCode' and 'destinationSuburb' elements ";
        $invalidPostCode .= "(within the 'Anywhere' type) do not contain valid values.  ";
        $invalidPostCode .= "These values must match with the predefined settings in the Temando system.";
        $errorsMap[$invalidPostCode] = __("Invalid suburb / postcode combination.");

        $genericErrorMessage = $this->_scopeConfig->getValue(
            'carriers/temando/specificerrmsg',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $errorsMap[self::ERR_INVALID_COUNTRY] = $genericErrorMessage;
        $errorsMap[self::ERR_INVALID_DEST] = $genericErrorMessage;
        $errorsMap[self::ERR_NO_METHODS] = $genericErrorMessage;
        $errorsMap[self::ERR_INTERNATIONAL] = $genericErrorMessage;
        $errorsMap[self::ERR_NO_ORIGIN] = $genericErrorMessage;
        // set the errors map
        $this->_errors_map = $errorsMap;
    }

    /**
     * Save shipping options in cache.
     *
     * @param $requestJson
     * @param $responseJson
     */
    private function _cacheSaveShippingOptions($request, $response)
    {
        $this->_cache->save($response, sha1($request), [self::CACHE_TAG], self::CACHE_LIFETIME);
    }

    /**
     * Load shipping options from cache.
     *
     * @param $requestJson
     *
     * @return bool|string
     */
    private function _cacheLoadShippingOptions($request)
    {
        return $this->_cache->load(sha1($request));
    }

    /**
     * Creates a string describing the applicable elements of a rate request.
     *
     * This is used to determine if the quotes fetched last time should be
     * refreshed, or if they can remain valid.
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest
     * @param int $salesQuoteId
     *
     * @return string
     */
    protected function _createRequestString(
        \Magento\Quote\Model\Quote\Address\RateRequest $rateRequest,
        $salesQuoteId
    ) {
        $deliveryOptions = $rateRequest->getDeliveryOptions();
        $authorityToLeave = 0;
        if (isset($deliveryOptions['unattended_delivery'])) {
            $authorityToLeave = 1;
        }

        $requestString = $salesQuoteId . '|';
        foreach ($rateRequest->getAllItems() as $item) {
            $requestString .= $item->getProductId() . 'x' . $item->getQty();
        }

        $requestString .= '|' . $rateRequest->getDestCity();
        $requestString .= '|' . $rateRequest->getDestCountryId();
        $requestString .= '|' . $rateRequest->getDestPostcode();
        $requestString .= '|' . $this->_origin->getId();
        $requestString .= '|' . $rateRequest->getDestType();
        $requestString .= '|' . $authorityToLeave;

        return $requestString;
    }
}
