<?php
/**
 * Pcs (AVS) Controller
 */

namespace Temando\Temando\Controller\Product;

use Magento\Framework\Escaper;
use \Temando\Temando\Model\Resource\Origin\Collection;

class Stockcheck extends \Magento\Framework\App\Action\Action
{
    /**
     * Object Manager
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * JSON Encoder
     *
     * @var \Magento\Framework\Json\Encoder
     */
    protected $_jsonEncoder;

    /**
     * Helper
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * Scope Config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Stockcheck constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Json\Encoder $encoder
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Encoder $encoder,
        \Temando\Temando\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_jsonEncoder = $encoder;
        $this->_scopeConfig = $scopeConfig;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Read params and find stock levels
     */
    public function execute()
    {
        $postcode = $this->getRequest()->getParam('postcode');//validate postcode
        $sku = $this->getRequest()->getParam('sku');

        $country = $this->getRequest()->getParam(
            'country',
            $this->_scopeConfig->getValue(
                'general/country/default',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );

        echo $this->_jsonEncoder->encode(
            $this->_helper->_getStockLevelsByPostcode($sku, $postcode, $country)
        );
        exit;
    }
}
