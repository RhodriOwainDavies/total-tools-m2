<?php

namespace Temando\Temando\Model\Api;

use Magento\Framework\Model\AbstractModel;

/**
 * Api Client
 */
class Client extends AbstractModel
{
    const NO_SHIP_DATE = 'Unable to send on the specified date and/or location.';

    protected $temandoApiSandbox = 'https://api-demo.temando.com/schema/2009_06/server.wsdl';
    protected $temandoApi = 'https://api.temando.com/schema/2009_06/server.wsdl';

    /**
     * Mode - Sandbox or Production?
     *
     * @var boolean
     */
    protected $_is_sand;

    /**
     * Client ID.
     *
     * @var
     */
    protected $_clientId;

    /**
     * API Client.
     *
     * @var
     */
    protected $_client;

    /**
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Scope Config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Object Manager.
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * Client constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_objectManager = $objectManager;
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
    }
    
    /**
     * Prepares Soap Client for API data exchange
     *
     * @param array $params Temando account details
     *
     * @return \Temando_Temando_Model_Api_Client
     */
    public function connect($params)
    {
        $this->_is_sand = $params['sandbox'];
        $this->_clientId = $params['clientid'];

        // The WSDL cache should be set to on to prevent the WSDL being loaded everytime.
        ini_set("soap.wsdl_cache_enabled", "1");

        // Create a new SoapClient referencing the Temando WSDL file.
        $soapClientOptions = array(
                'soap_version' => SOAP_1_2,
                'trace' => true,
                'exceptions' => false
            );
        
        if ($this->_scopeConfig->getValue(
            'temando/general/sandbox',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            $url = $this->temandoApiSandbox;
        } else {
            $url = $this->temandoApi;
        }
            
        $headerSecurityStr = "<Security><UsernameToken><Username>" . htmlentities($params['username']) . "</Username>" .
                "<Password>" . htmlentities($params['password']) . "</Password></UsernameToken></Security>";

        $headerSecurityVar = new \SoapVar($headerSecurityStr, XSD_ANYXML);
        $soapHeader = new \SoapHeader(
            'wsse:http://schemas.xmlsoap.org/ws/2002/04/secext',
            'soapenv:Header',
            $headerSecurityVar
        );
        
        // create SoapClient object
        try {
            if ($this->_scopeConfig->getValue(
                'temando/developer/logs',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )) {
                $this->_clientFactory = $this->_objectManager->create(
                    'Temando\Temando\Model\Api\TemandoSoapClientFactory'
                );
                $this->_client = $this->_clientFactory->create(
                    $url,
                    $this->_logger,
                    $soapClientOptions
                );
            } else {
                $this->_clientFactory = $this->_objectManager->create('Magento\Framework\Webapi\Soap\ClientFactory');
                $this->_client = $this->_clientFactory->create($url, $soapClientOptions);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        // set SoapClient header
        $this->_client->__setSoapHeaders($soapHeader);

        return $this;
    }

    /**
     * Makes API call to fetch real-time shipping quotes and returns array of Temando Quotes Objects
     *
     * @param array $request
     *
     * @return array|bool
     */
    public function getQuotes($request)
    {
        if (!$this->_client) {
            $this->_logger->debug(get_class($this) . __(' Client Object is null'));
            return false;
        }

        if (!$this->_is_sand) {
            $request['clientId'] = $this->_clientId;
        }

        $response = $this->_client->getQuotes($request);

        //check for no ship days - public holidays - and retry with date +1 (up to +10) days before exiting
        $i = 0;
        while (is_soap_fault($response) && $response->faultstring == self::NO_SHIP_DATE && ++$i <= 10) {
            $readyDate = strtotime($request['anytime']['readyDate']);
            while (in_array(date('N', $readyDate), array(6, 7))) {
                $readyDate = strtotime('+1 day', $readyDate);
            }
            $request['anytime']['readyDate'] = date('Y-m-d', strtotime('+1 day', $readyDate));
            $response = $this->_client->getQuotes($request);
        }

        if (is_soap_fault($response)) {
            $this->_logger->debug($response->faultstring);
            return $response;
        }
        if (!isset($response->quotes->quote)) {
            $response->quotes->quote = array();
        } elseif (isset($response->quotes->quote) && !is_array($response->quotes->quote)) {
            $response->quotes->quote = array(0 => $response->quotes->quote);
        }

        $quotes = array();
        foreach ($response->quotes->quote as $quoteData) {
            $q = $this->_objectManager->create('Temando\Temando\Model\Quote');
            $quotes[] = $q->loadResponse($quoteData);
        }
        return $quotes;
    }
    
    /**
     * Get a list of locations
     *
     * @param array $request
     *
     * @return boolean|stdClass
     *
     * @throws Exception
     */
    public function getLocations($request)
    {
        if (!$this->_client) {
            return false;
        }
        $response = $this->_client->getLocations($request);
        if (is_soap_fault($response)) {
            $this->_logger->error($response->faultstring);
        }
        return $response;
    }

    /**
     * Create a location.
     *
     * @param array $request
     *
     * @return boolean|stdClass
     *
     * @throws Exception
     */
    public function createLocation($request)
    {
        if (!$this->_client) {
            return false;
        }
        $request['clientId'] = $this->_clientId;

        $response = $this->_client->createLocation($request);
        if (is_soap_fault($response)) {
            $this->_logger->error($response->faultstring);
        }
        return $response;
    }

    /**
     * Update a location.
     *
     * @param array $request
     *
     * @return boolean|stdClass
     *
     * @throws Exception
     */
    public function updateLocation($request)
    {
        if (!$this->_client) {
            return false;
        }
        $request['clientId'] = $this->_clientId;

        $response = $this->_client->updateLocation($request);
        if (is_soap_fault($response)) {
            $this->_logger->error($response->faultstring);
        }
        return $response;
    }


    /**
     * Makes a booking for a delivery
     *
     * @param array $request
     *
     * @return boolean|stdClass
     *
     * @throws Exception
     */
    public function makeBooking($request)
    {
        if (!$this->_is_sand) {
            $request['clientId'] = $this->_clientId;
        }
        if (!$this->_client) {
            return false;
        }

        $response = $this->_client->makeBooking($request);
        if (is_soap_fault($response)) {
            //throw new Exception($response->faultstring);
            //echo $response->faultstring;exit;
            $this->_logger->error($response->faultstring);
        }
        return $response;
    }
}
