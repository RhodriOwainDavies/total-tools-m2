<?php
/**
 * Pcs (AVS) Controller
 */

namespace Temando\Temando\Controller\Api;

use Magento\Framework\Escaper;

class Pcs extends \Magento\Framework\App\Action\Action
{
    /**
     * Validator.
     *
     * @var \Temando\Temando\Model\Pcs
     */
    protected $_validator;

    protected $_objectManager;

    protected $_jsonEncoder;

    protected $_logger;

    protected $_scopeConfig;

    /**
     * Escaper.
     *
     * @var Escaper
     */
    protected $_escaper;

    private $_result = array (
            'query' => '',
            'suggestions' => array(),
            'data' => array(
                0 => array (
                    0 => array(
                            'city' => '',
                            'region_id' => '',
                            'postcode' => ''
                        )
                    )
            )
        );

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\Encoder $encoder,
        \Temando\Temando\Model\Pcs $pcs,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Escaper $escaper
    ) {
        parent::__construct($context);
        $this->_logger = $logger;
        $this->_validator = $pcs;
        $this->_jsonEncoder = $encoder;
        $this->_scopeConfig = $scopeConfig;
        $this->_escaper = $escaper;
    }

    
    /**
     * Autocomplete suggestion of post code and suburb
     */
    public function execute()
    {
        $query = $this->getRequest()->getParam('query');
        $country = $this->getRequest()->getParam(
            'country',
            $this->_scopeConfig->getValue(
                'general/country/default',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
        echo $this->_makeAutocomplete($query, $country);
        exit;
    }
    /**
     * Make the autocomplete
     *
     * @param string $query
     * @param string $country
     *
     * @return json
     */
    protected function _makeAutocomplete($query, $country = null)
    {
        $country =  $country ? $country : $this->_scopeConfig->getValue(
            'general/country/default',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $this->_result['query'] = $this->_escaper->escapeHtml($query);
        $this->_getValidator();
        $this->_validator->setCountry($country)->setQuery($query);
        $suggestions = $this->_validator->getSuggestions();

        $i = -1;
        if (!empty($suggestions)) {
            //have results - load into result array
            $this->_result['data'] = array();
            foreach ($suggestions as $item) {
                $fulltext = $item['name'] .', ';
                $fulltext.= array_key_exists('postcodes', $item) &&
                    !empty($item['postcodes']) ?
                    $item['postcodes'][0]['code'].' ' : ' ';
                $fulltext.= $item['country']['iso_code2'];

                if (!in_array($fulltext, $this->_result['suggestions'])) {
                    $i++;
                    $this->_result['suggestions'][$i] = $fulltext;
                }
                $this->_result['data'][$i][] = array(
                    'postcode'  => array_key_exists('postcodes', $item) &&
                        !empty($item['postcodes']) ?
                        $item['postcodes'][0]['code'] : '',
                    'city'  => $item['name'],
                    'country_id'=> $item['country']['iso_code2'],
                    'fulltext'  => $fulltext
                );
            }
        }
        $result = $this->_jsonEncoder->encode($this->_result);
        return $result;
    }

    /**
     * Validate
     *
     * @return \Temando\Temando\Controller\Api\Pcs
     */
    protected function _getValidator()
    {
        if (!$this->_validator) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_validator = $objectManager->create('Temando\Temando\Model\Pcs');
        }
        return $this;
    }
}
