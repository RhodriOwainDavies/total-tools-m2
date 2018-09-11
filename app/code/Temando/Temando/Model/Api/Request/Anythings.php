<?php

namespace Temando\Temando\Model\Api\Request;

use Magento\Framework\Model\AbstractModel;

/**
 * Api Request Anythings
 */
class Anythings extends AbstractModel
{
    /**
     * Anythings.
     *
     * @var array
     */
    protected $_anythings;

    /**
     * Need optimize.
     *
     * @var bool
     */
    protected $_need_optimize = false;

    /**
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Anything.
     *
     * @var \Temando\Temando\Model\Api\Request\Anything
     */
    protected $_anything;

    /**
     * Scope Config.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Temando Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * Object Manager.
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Temando\Temando\Helper\Data $helper,
        \Temando\Temando\Model\Api\Request\Anything $anything
    ) {
        $this->_logger = $context->getLogger();
        $this->_scopeConfig = $scopeConfig;
        $this->_helper = $helper;
        $this->_anything = $anything;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_objectManager = $objectManager;
        parent::__construct($context, $registry);
    }
    
    public function _construct()
    {
        $this->_anythings = array();
    }

    /**
     * Set items
     *
     * @param mixed $items
     *
     * @return \Temando\Temando\Model\Api\Request\Anythings
     */
    public function setItems($items)
    {
        $this->_anythings = array();
        foreach ($items as $item) {//item is an array
            $this->addItem($item);
        }
        return $this;
    }

    /**
     * Add an item
     *
     * @param mixed $item
     *
     * @return \Temando\Temando\Model\Api\Request\Anythings
     */
    public function addItem($item)
    {
        $cleanAnything = $this->_objectManager->create('\Temando\Temando\Model\Api\Request\Anything');
        $this->_anythings[] = $cleanAnything->setItem($item);
        return $this;
    }

    /**
     * Get the items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->_anythings;
    }

    /**
     * Prepares array of anythings for XML API SOAP call
     *
     * @return array
     */
    public function toRequestArray($origCountry, $destCountry, $articles = null)
    {
        $output = array();
        foreach ($this->_anythings as $anything) {
            //each anything is a box with pre-configured contents
            $articles = $anything->toRequestArray($origCountry, $destCountry);
            foreach ($articles as $request) {
                $output[] = $request;
            }
        }
        return $output;
    }

    /**
     * Returns combined value of request items.
     *
     * @return float
     */
    public function getGoodsValue()
    {
        $goodsValue = 0;
        foreach ($this->_anythings as $anything) {
            /* @var $anything Temando\Temando\Model\Api\Request\Anything */
            $item = $anything->getItem();
            if ($item instanceof Temando\Temando\Model\Box) {
                $goodsValue += $item->getValue();
            } else {
                //$goodsValue += ($item->getQty() * Mage::helper('temando')->getItemArticleValue($item));
            }
        }
        return (float) $goodsValue;
    }
}
