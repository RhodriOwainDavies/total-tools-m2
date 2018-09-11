<?php
/**
 * Pcs (AVS) Model
 *
 * @package Temando_Temando
 *
 * @method Temando\Temando\Model\Pcs setCountry()
 * @method Temando\Temando\Model\Pcs setQuery()
 *
 * @method string getCountry()
 * @method string getQuery()
 */

namespace Temando\Temando\Model;

class Pcs extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Address validation Temando AVS URL
     */
    const AVS_URL = "http://avs.temando.com/avs/search/country/%s/%s.json?limit=1000";

    /**
     * The HTTP Client
     *
     * @var \Magento\Framework\HTTP\ZendClient
     */
    protected $_client;

    protected $_jsonDecoder;

    /**
     * Default country (only one country in allowed countries)
     *
     * @var string
     */
    protected $_defaultCountry = null;
    
    /**
     * Temando Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;
    
    protected $_logger;
    
    public function _construct()
    {
        parent::_construct();
        $this->_prepareClient()->_loadDefaultCountry();
    }

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     */
    public function __construct(
        \Temando\Temando\Helper\Data $helper,
        \Magento\Framework\HTTP\ZendClient $client,
        \Magento\Framework\Json\Decoder $jsonDecoder,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_helper = $helper;
        $this->_client = $client;
        $this->_jsonDecoder = $jsonDecoder;
        $this->_logger = $logger;
    }
    
    /**
     * Returns address postcode/country combinations as an array.
     * Empty array is returned if no suggestions are found.
     *
     * @return array
     */
    public function getSuggestions()
    {
        if (!$this->_validate()) {
            return array();
        }
        $url = sprintf(self::AVS_URL, strtoupper($this->getCountry()), rawurlencode($this->getQuery()));

        try {
            $this->_client->setUri($url);
            $rawBody = $this->_client->request('GET')->getRawBody();
            return $this->_jsonDecoder->decode($rawBody);
        } catch (Exception $e) {
            $this->_logger->debug($e->getMessage());
            return array();
        }
    }

    /**
     * Checks current request - country & query
     *
     * @return boolean
     */
    protected function _validate()
    {
        if (strlen(trim($this->getCountry())) === 0 && $this->_defaultCountry) {
            $this->setCountry($this->_defaultCountry);
        }

        return  strlen(trim($this->getCountry())) > 0 &&
                strlen(trim($this->getQuery())) > 0;
    }

    /**
     * Initializes http client to communicate with AVS service
     *
     * @return \Temando\Temando\Model\Pcs
     */
    protected function _prepareClient()
    {
        if (!$this->_client) {
            $this->_client->setConfig(array('maxredirects' => 2, 'timeout' => 15));
        }
        return $this;
    }

    /**
     * Loads default destination country
     *
     * @return \Temando\Temando\Model\Pcs
     */
    protected function _loadDefaultCountry()
    {
        if (is_null($this->_defaultCountry)) {
            $allowed = $this->_helper->getAllowedCountries();
            if (count($allowed) === 1) {
                //one allowed country - load as default
                reset($allowed);
                $this->_defaultCountry = key($allowed);
            }
        }
        return $this;
    }
}
