<?php

namespace Temando\Temando\Model\Api\Request;

use Magento\Framework\Model\AbstractModel;

/**
 * Api Request Anything
 */
class Anything extends AbstractModel
{
    const GOODS_CLASS    = 'General Goods';
    const GOODS_SUBCLASS = 'Household Goods';
    const PALLET_TYPE    = 'Plain';
    const PALLET_NATURE  = 'Not Required';

    /**
     * Magento Sales Order Item.
     *
     * @var \Magento\Sales\Model\Order\Item
     */
    protected $_item;

    /**
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Packaging.
     *
     * @var
     */
    protected $_packaging;

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


    public function _construct()
    {
    }

    /**
     * Anything constructor.
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Temando\Temando\Helper\Data $helper
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Temando\Temando\Helper\Data $helper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_logger = $logger;
        $this->_helper = $helper;
    }

    /**
     * Set an item
     *
     * @param mixed $item
     *
     * @return \Temando\Temando\Model\Api\Request\Anything
     */
    public function setItem($item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Gets the order item for this Anything object.
     *
     * @return Mage_Sales_Model_Order_Item
     */
    public function getItem()
    {
        if ($this->_item) {
            return $this->_item;
        }
        return false;
    }

    /**
     * Prepares the anything request array
     *
     * @param string $origCountry
     * @param string $destCountry
     *
     * @return boolean|array
     */
    public function toRequestArray($origCountry, $destCountry)
    {
        $international = $origCountry != $destCountry;
        $anythings = array();
        //this item is an array
        $itemWeight = $this->_item['weight'];
        $itemLength = $this->_item['length'];
        $itemWidth = $this->_item['width'];
        $itemHeight = $this->_item['height'];

        $weight = $this->_helper->getWeightInGrams(
            $itemWeight ? $itemWeight : $this->_scopeConfig->getValue(
                'temando/defaults/weight',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );

        $length = $this->_helper->getDistanceInCentimetres(
            $itemLength ? $itemLength : $this->_scopeConfig->getValue(
                'temando/defaults/length',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );

        $width = $this->_helper->getDistanceInCentimetres(
            $itemWidth ? $itemWidth : $this->_scopeConfig->getValue(
                'temando/defaults/width',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );

        $height = $this->_helper->getDistanceInCentimetres(
            $itemHeight ? $itemHeight : $this->_scopeConfig->getValue(
                'temando/defaults/height',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );

        if (strlen($this->_item['comment'])>950) {
            $comment = substr($this->_item['comment'], 0, 950) . '...';
        } else {
            $comment = $this->_item['comment'];
        }

        $anythings[] = array(
            'class' => self::GOODS_CLASS,
            'subclass' => self::GOODS_SUBCLASS,
            'packaging' => 'Box', //default to Box
            'quantity' => 1,//(int)($this->_item['qty']),
            'distanceMeasurementType' => \Temando\Temando\Model\System\Config\Source\Unit\Measure::CENTIMETRES,
            'weightMeasurementType' => \Temando\Temando\Model\System\Config\Source\Unit\Weight::GRAMS,
            'weight' => $weight,
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'qualifierFreightGeneralFragile' => $this->_item['fragile'] == '1' ? 'Y' : 'N',
            'qualifierFreightGeneralDangerousGoods' => $this->_item['dangerous'] == '1' ? 'Y' : 'N',
            'description' => $comment,
            'articles' => $this->_item['articles']
        );

        return $anythings;
    }
}
