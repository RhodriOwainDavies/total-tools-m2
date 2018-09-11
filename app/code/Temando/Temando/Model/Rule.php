<?php

namespace Temando\Temando\Model;

use Magento\Quote\Model\Quote\Address;
use Magento\Rule\Model\AbstractModel;

/**
 * Class Rule
 *
 * @package Vendor\Rules\Model
 *
 * @method int|null getRuleId()
 * @method Rule setRuleId(int $id)
 */
class Rule extends AbstractModel
{
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'temando_rules';

    /**
     * Parameter name in event.
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';

    /**
     * Combine Factory.
     *
     * @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    protected $_condCombineFactory;

    /**
     * Rule Product Combine Factory.
     *
     * @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    protected $_condProdCombineF;
    protected $_session;
    protected $_logger;
    protected $_cart;
    protected $_productFactory;
    protected $_helper;
    protected $_backendSession;
    protected $_appState;

    /**
     * Store already validated addresses and validation results
     *
     * @var array
     */
    protected $validatedAddresses = [];

    /**
     * Rule constructor.
     *
     * @param \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory
     * @param \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF
     * @param \Magento\Checkout\Model\Session $session
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Magento\Backend\Model\Session\Quote $backendSession
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF,
        \Magento\Checkout\Model\Session $session,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Temando\Temando\Helper\Data $helper,
        \Magento\Backend\Model\Session\Quote $backendSession,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_condCombineFactory = $condCombineFactory;
        $this->_condProdCombineF = $condProdCombineF;
        $this->_session = $session;
        $this->_logger = $context->getLogger();
        $this->_cart = $cart;
        $this->_productFactory = $productFactory;
        $this->_helper = $helper;
        $this->_backendSession = $backendSession;
        $this->_appState = $context->getAppState();
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * Set resource model and Id field name.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Temando\Temando\Model\ResourceModel\Rule');
        $this->setIdFieldName('rule_id');
    }

    /**
     * Get rule condition combine model instance.
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->_condCombineFactory->create();
    }

    /**
     * Get rule condition product combine model instance.
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        return $this->_condProdCombineF->create();
    }

    /**
     * Check cached validation result for specific address.
     *
     * @param Address $address
     *
     * @return bool
     */
    public function hasIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->validatedAddresses[$addressId]) ? true : false;
    }

    /**
     * Set validation result for specific address to results cache.
     *
     * @param Address $address
     * @param bool $validationResult
     *
     * @return $this
     */
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->_getAddressId($address);
        $this->validatedAddresses[$addressId] = $validationResult;
        return $this;
    }

    /**
     * Get cached validation result for specific address.
     *
     * @param Address $address
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->validatedAddresses[$addressId]) ? $this->validatedAddresses[$addressId] : false;
    }

    /**
     * Return id for address.
     *
     * @param Address $address
     *
     * @return string
     */
    private function _getAddressId($address)
    {
        if ($address instanceof Address) {
            return $address->getId();
        }
        return $address;
    }


    /**
     * Validates current date against the configured range (from/to)
     * of this rule
     *
     * @return boolean true if now is within dates, false otherwise
     */
    public function validateDate()
    {
        $current_date = strtotime(date('Y-m-d'));

        if ($fromDate = $this->getData('from_date')) {
            $from = strtotime($fromDate);
            if ($current_date<$from) {
                return false;
            }
        }
        if ($toDate = $this->getData('to_date')) {
            $to = strtotime($toDate);
            if ($current_date>$to) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if rule satisfies current conditions
     *
     * @param $object
     *
     * @return boolean
     */
    public function isValid($object)
    {
        if (!$this->getAttributeSetId()) {
            return true;
        }

        $salesQuote = null;

        if ($this->_helper->isAdmin($this->_appState)) {
            $salesQuote = $this->_backendSession->getQuote();
        } else {
            $salesQuote = $this->_session->getQuote();
        }

        $items = $salesQuote->getAllVisibleItems();
        foreach ($items as $_key => $_item) {
            $product = $this->_productFactory->create();
            $product = $product->loadByAttribute('sku', $_item->getSku());
            if ($this->getAttributeSetId() != $product->getAttributeSetId()) {
                return false;
            }
        }

        return true;
        return parent::validate($object);
    }

    /**
     * Does this rule return dynamic carrier quote?
     *
     * @return boolean
     */
    public function isDynamic()
    {
        if ($this->getActionRateType() == \Temando\Temando\Model\System\Config\Source\Rule\Type::DYNAMIC) {
            return true;
        }
        return false;
    }
}
