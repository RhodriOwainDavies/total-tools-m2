<?php

namespace Temando\Temando\Model\Api;

use Magento\Framework\Model\AbstractModel;

class Request extends AbstractModel
{
    /**
     * Anythings.
     *
     * @var \Temando\Temando\Model\Api\Request\Anythings
     */
    protected $_anythings;

    /**
     * Anywhere.
     *
     * @var \Temando\Temando\Model\Api\Request\Anywhere
     */
    protected $_anywhere ;

    /**
     * Anytime.
     *
     * @var \Temando\Temando\Model\Api\Request\Anytime
     */
    protected $_anytime = null;

    /**
     * If request includes anytime component.
     *
     * @var boolean
     */
    protected $use_anytime = false;

    /**
     * Quotes.
     *
     * @var array
     */
    protected $_quotes;

    /**
     * API Client.
     *
     * @var \Temando\Temando\Model\Api\Client
     */
    protected $_client;

    /**
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Quote Collection.
     *
     * @var \Temando\Temando\Model\ResourceModel\Quote\Collection
     */
    protected $_quoteCollection;

    /**
     * Object Manager.
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    public function _construct()
    {
    }
    
    public function __construct(
        \Temando\Temando\Model\Api\Request\Anythings $anythings,
        \Temando\Temando\Model\Api\Request\Anything $anything,
        \Temando\Temando\Model\Api\Request\Anywhere $anywhere,
        \Temando\Temando\Model\Api\Request\Anytime $anytime,
        \Temando\Temando\model\Api\Client $client,
        \Psr\Log\LoggerInterface $logger,
        \Temando\Temando\Model\ResourceModel\Quote\Collection $quoteCollection
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_objectManager = $objectManager;
        $this->_anythings = $anythings;
        $this->_anything = $anything;
        $this->_anywhere = $anywhere;
        $this->_anytime = $anytime;
        $this->_client = $client;
        $this->_logger = $logger;
        $this->_quoteCollection = $quoteCollection;
    }

     /**
     * Set the anythings items
     *
     * @param mixed $items
      *
     * @return \Temando\Temando\Model\Api\Request
     */
    public function setItems($items)
    {
        $this->_anythings->setItems($items);
        return $this;
    }

    /**
     * Set the destination
     *
     * @param string $country
     * @param string $postcode
     * @param string $city
     * @param string $street
     * @param string $destinationType
     *
     * @return \Temando\Temando\Model\Api\Request
     */
    public function setDestination(
        $country,
        $postcode,
        $city,
        $street = null,
        $destinationType = 'residence'
    ) {
        $this->_anywhere
            ->setDestinationCountry($country)
            ->setDestinationPostcode($postcode)
            ->setDestinationCity($city)
            ->setDestinationStreet($street)
            ->setDestinationType($destinationType)
            ->setDeliveryOptions($this->getDeliveryOptions());
        return $this;
    }

    /**
     * Set the origin
     *
     * @param string $description
     * @param string $country
     *
     * @return \Temando_Temando_Model_Api_Request
     */
    public function setOrigin($description, $country = null)
    {
        $country = $country ? $country : 'AU';
        $this->_anywhere->setOriginDescription($description)->setOriginCountry($country);
        return $this;
    }

    /**
     * Set the ready date
     *
     * @param date $timestamp
     * @param string $time_of_day
     *
     * @return \Temando\Temando\Model\Api\Request
     */
    public function setReady($timestamp = null, $time_of_day = 'AM')
    {
        if (!is_null($timestamp)) {
            $this->use_anytime = true;
        }

        $this->_anytime
            ->setReadyDate($timestamp)
            ->setReadyTimeOfDay($time_of_day);
        return $this;
    }

    /**
     * Gets all available Temando quotes for this request.
     *
     * @return \Temando\Temando\Model\ResourceModel\Quote\Collection
     */
    public function getQuotes()
    {
        $fetchQuotesResult = $this->_fetchQuotes();
        if (is_soap_fault($fetchQuotesResult)) {
            // validation failed
            $this->_logger->debug(get_class($this) . ' getQuotes() Found SOAP Fault');
            return $fetchQuotesResult;
        }

        $quoteCollection = $this->_objectManager->create('\Temando\Temando\Model\ResourceModel\Quote\Collection');
        $quotes = $quoteCollection
            ->addFieldToFilter('magento_quote_id', $this->getMagentoQuoteId())->load();

        return $quotes;
    }
    
    /**
     * Fetches the quotes and saves them into the database.
     *
     * @throws Exception
     */
    protected function _fetchQuotes()
    {
        $request = $this->toRequestArray();
        
        if (!$request) {
            return false;
        }
                
        try {
            $this->_client->connect($this->getConnectionParams());
            $quotes = $this->_client->getQuotes($request);
        } catch (Exception $e) {
            $this->_logger->debug(get_class($this) . ' _fetchQuotes() Exception ' . $e->getMessage());
            throw $e;
        }
        if (is_soap_fault($quotes)) {
            $this->_logger->debug(get_class($this) . ' _fetchQuotes() SOAP FAULT ' . $quotes->faultstring);
            return $quotes;
        }
        // filter by allowed carriers, if the filter has been set
        $filtered_quotes = $quotes;
        if (is_array($this->getAllowedCarriers())) {
            $filtered_quotes = array();
            foreach ($quotes as $quote) {
                /* @var $quote \Temando\Temando\Model\Quote */
                $quoteCarrierMethod = $quote->getCarrier()->getServiceByMethod($quote->getDeliveryMethod());
                if (!$quoteCarrierMethod) {
                    continue;
                }
            }
        }
        $this->_saveQuotes($quotes);

        return true;
    }
    
     /**
     * Prepares the request array
     *
     * @return boolean|array
     */
    public function toRequestArray()
    {
        $international = $this->_anywhere->getOriginCountry() != $this->_anywhere->getDestinationCountry();

        $goodsValue = $this->_anythings->getGoodsValue();

        $return = array(
            'anythings' => $this
                ->_anythings
                ->toRequestArray(
                    $this->_anywhere->getOriginCountry(),
                    $this->_anywhere->getDestinationCountry(),
                    $this->getArticles()
                ),
            'anywhere' => $this
                ->_anywhere
                ->setDeliveryOptions($this->getDeliveryOptions())
                ->toRequestArray($international),
        );

        if ($goodsValue) {
            $return['general'] = array(
                'goodsValue' => round($goodsValue, 2),
                'goodsCurrency' => $this->getGoodsCurrency()
            );
        }

        //check international shipment
//        if ($this->_anywhere->getOriginCountry() != $this->_anywhere->getDestinationCountry()) {
//            $return['general']['termsOfTrade'] = Mage::helper('temando')->getDutiesAndTaxesType(
//                $this->_anywhere->getOriginCountry(),
//                $this->_anywhere->getDestinationCountry()
//            );
//        }
        if ($this->use_anytime) {
            $return['anytime'] = $this->_anytime->toRequestArray();
        }

        return $return;
    }
    
    /**
     * Saves an array of quotes to the database.
     *
     * @param array $quotes an array of \Temando\Temando\Model\Quote objects.
     */
    protected function _saveQuotes($quotes)
    {
        // delete all old Temando quotes for this Magento quote
        $quoteCollection = $this->_objectManager->create('Temando\Temando\Model\ResourceModel\Quote\Collection');

//        $old_quotes = $this->_quoteCollection
//            ->addFieldToFilter('magento_quote_id', $this->getMagentoQuoteId())->load();

        $old_quotes = $quoteCollection
            ->addFieldToFilter('magento_quote_id', $this->getMagentoQuoteId())->load();

        foreach ($old_quotes as $quote) {
            /* @var $quote \Temando\Temando\Model\Quote */
            $quote->delete();
        }
        // add new Temando quotes to the database
        foreach ($quotes as $quote) {
            $q = $this->_objectManager->create('Temando\Temando\Model\Quote');
            $q->setData($quote->getData());
            $q->setMagentoQuoteId($this->getMagentoQuoteId())
                ->save();
        }
        return $this;
    }
}
