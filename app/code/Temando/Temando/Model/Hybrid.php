<?php
/**
 * Hybrid Model - Rules Processing
 */
namespace Temando\Temando\Model;

use Magento\Framework\Model\AbstractModel;

class Hybrid extends AbstractModel
{
    /**
     * Pricing Method Code
     */
    const METHOD_CODE  = 'engine';

    /**
     * All valid rules filtered by current conditions
     *
     * @var array
     */
    protected $_validRules;
    
    /**
     * Flag if any of the valid rules are dynamic
     *
     * @var boolean
     */
    protected $_hasDynamic = null;

    /**
     * Flag if any of the valid rules is restrictive (shipping not allowed)
     *
     * @var boolean
     */
    protected $_hasRestrictive = null;

    /**
     * Text to display to customer when restrictive rule setup
     *
     * @var string
     */
    protected $_restrictiveNote = '';

    /**
     * Temando Helper.
     *
     * @var Temando_Temando_Helper_Data
     */
    protected $_helper;

    /**
     * Functions Helper.
     *
     * @var \Temando\Temando\Helper\Functions
     */
    protected $_functions;

    /**
     * Rule Collection.
     *
     * @var ResourceModel\Rule\Collection
     */
    protected $_ruleCollection;

    /**
     * Logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Rate Result Factory.
     *
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * Store Manager.
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Customer Session.
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Rule Type.
     *
     * @var System\Config\Source\Rule\Type
     */
    protected $_type;

    /**
     * ScopeConfig.
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Hybrid constructor.
     *
     * @param \Temando\Temando\Helper\Data $helper
     * @param \Temando\Temando\Helper\Functions $functions
     * @param ResourceModel\Rule\Collection $ruleCollection
     * @param System\Config\Source\Rule\Type $type
     * @param Shipping\Carrier\Temando $carrier
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Temando\Temando\Helper\Data $helper,
        \Temando\Temando\Helper\Functions $functions,
        \Temando\Temando\Model\ResourceModel\Rule\Collection $ruleCollection,
        \Temando\Temando\Model\System\Config\Source\Rule\Type $type,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_validRules = array();
        $this->_helper = $helper;
        $this->_functions = $functions;
        $this->_ruleCollection = $ruleCollection;
        $this->_logger = $context->getLogger();
        $this->_type = $type;
        $this->_scopeConfig = $scopeConfig;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Constructor.
     */
    public function _construct()
    {
    }

    /**
     * Loads all rules based on the current request conditions
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return \Temando\Temando\Model\Hybrid
     */
    public function loadRules(\Magento\Quote\Model\Quote $quote)
    {
        $collection = $this->_ruleCollection;

        $collection->addFieldToFilter('is_active', '1')
            ->setOrder('priority', 'ASC');

        $store_id = $this->_storeManager->getStore()->getId();

        $group_id = $this->_customerSession->getCustomerGroupId();
        foreach ($collection->getItems() as $rule) {
            /* @var $rule \Temando\Temando\Model\Rule */
            if (!$rule->validateDate()) {
                continue;
            }
            $store_ids = explode(',', $rule->getStoreIds());
            $group_ids = explode(',', $rule->getGroupIds());

            if (in_array($store_id, $store_ids) && $rule->isValid($quote)) {
                $this->_validRules[] = $rule;
            }
        }

        return $this;
    }

    /**
     * Is there a valid shipping rule which can return shipping method?
     *
     * @return boolean
     */
    public function hasValidRules()
    {
        return !empty($this->_validRules);
    }

    /**
     * Checks if there is a dynamic rule which needs to be processed
     *
     * @return boolean true if dynamic rule exist,
     * false otherwise or when rules are not loaded
     */
    public function hasDynamic()
    {
        if (is_null($this->_hasDynamic)) {
            $this->_hasDynamic = false;
            if (!empty($this->_validRules)) {
                foreach ($this->_validRules as $rule) {
                    if ($rule->isDynamic()) {
                        $this->_hasDynamic = true;
                        break;
                    }
                }
            }
        }
        return $this->_hasDynamic;
    }

    /**
     * Checks if there is a restrictive rule which cancels all
     *
     * @return boolean true if restrictive rule exist,
     * false otherwise or when rules are not loaded
     */
    public function hasRestrictive()
    {
        return false;
        if (is_null($this->_hasRestrictive)) {
            $this->_hasRestrictive = false;
            if (!empty($this->_validRules)) {
                foreach ($this->_validRules as $rule) {
                    $this->_hasRestrictive = true;
                    $this->_restrictiveNote = $rule->getActionRestrictNote();
                    break;
                }
            }
        }
        return $this->_hasRestrictive;
    }

    /**
     * Returns the custom message configured on restrictive rule
     *
     * @return string
     */
    public function getRestrictiveNote()
    {
        return $this->_restrictiveNote;
    }

    /**
     * Returns list of available shipping methods (Mage_Shipping_Model_Rate_Result_Method)
     *
     * @param array $quotes
     *
     * @return array List of available shipping method rates
     */
    public function getShippingMethods($quotes)
    {
        $methods = array();
        $stopOnNext = false;
        $stopPriorityAfter = null;

        $carrierTitle = $this->_scopeConfig->getValue(
            'carriers/temando/title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        foreach ($this->_validRules as $rule) {
            /* @var $rule Temando_Temando_Model_Rule */
            $priority = $rule->getPriority();
            //stop if previous rule has stopOther and higher priority
            if ($stopOnNext && $priority > $stopPriorityAfter) {
                break;
            }
            if ($rule->isDynamic()) {
                //can't build ship method from dynamic rule without quotes
                if (empty($quotes)) {
                    continue;
                }
                $ruleQuotes = $this->_processDynamicRule($rule, $quotes);

                //check if we have any quotes left after we've applied filters
                if (empty($ruleQuotes)) {
                    continue;
                }
                //process quotes - apply extras and create methods
                foreach ($ruleQuotes as $ruleQuote) {
                    $title = $rule->getActionDynamicLabel();

                    $method = $this->_rateMethodFactory->create();
                    $methodId = "temando_".$ruleQuote->getData('quote_id');

                    $methods[]  = $method
                        ->setCarrier(\Temando\Temando\Model\Shipping\Carrier\Temando::getCode())
                        ->setCarrierTitle($carrierTitle)
                        ->setMethodTitle($title)
                        ->setMethod($methodId)
                        ->setPrice($this->_processPrice($rule, $ruleQuote->getTotalPrice()))
                        ->setCost($ruleQuote->getBasePrice())
                        ->setRuleQuoteId($ruleQuote->getId())
                        ->setRuleId($rule->getId());
                }
            }
            //stop further rules processing check
            if ($rule->getStopOther()) {
                $stopOnNext = true;
                $stopPriorityAfter = $priority;
            }
        }
        //Mage::unregister('temando_rule_items');

        return $methods;
    }

    /**
     * Applies the per item cost if enabled in the rule
     *
     * @param Temando_Temando_Model_Rule $rule
     * @param decimal $price
     *
     * @return decimal
     */
    protected function _processPrice(\Temando\Temando\Model\Rule $rule, $price)
    {
        $returnPrice = $price;
        if ($rule->getActionDynamicAdjustmentType() ==
            \Temando\Temando\Model\System\Config\Source\Rule\Action\Adjustment::OVERRIDE
        ) {
            return $rule->getActionDynamicAdjustmentValue();
        } elseif ($rule->getActionDynamicAdjustmentType()
            == \Temando\Temando\Model\System\Config\Source\Rule\Action\Adjustment::MARKUP
        ) {
            $returnPrice = round(($rule->getActionDynamicAdjustmentValue()*$price), 2);
        }
        if ($rule->getActionDynamicAdjustmentRoundup()) {
            return ceil($returnPrice);
        }
        return $returnPrice;
    }

    /**
     * Applies rule filters and adjustments to available shipping quotes (carrier quotes from API)
     *
     * @param Temando_Temando_Model_Rule $rule
     * @param array $quotes
     *
     * @return array
     */
    protected function _processDynamicRule(\Temando\Temando\Model\Rule $rule, $quotes)
    {
        //apply carrier filter
        $carriers = explode(',', $rule->getActionDynamicCarriers());
        $cleanQuotes = array();

        foreach ($quotes as $quote) {
            /* @var $quote \Temando\Temando\Model\Quote */
            $cleanQuotes[] = clone $quote;
        }

        if (empty($cleanQuotes)) {
            return array();
        }

        //apply pricing method filter
        switch ($rule->getActionDynamicFilter()) {
            case \Temando\Temando\Model\System\Config\Source\Rule\Action\Filter::DYNAMIC_CHEAPEST:
                $cleanQuotes = $this->_functions->getCheapestQuote($cleanQuotes);
                break;
            case \Temando\Temando\Model\System\Config\Source\Rule\Action\Filter::DYNAMIC_FASTEST:
                $cleanQuotes = $this->_functions->getFastestQuote($cleanQuotes);
                break;
        }

        if (!is_array($cleanQuotes)) {
            $cleanQuotes = array($cleanQuotes);
        }

        //process price adjustments
        return $cleanQuotes;
    }
}
