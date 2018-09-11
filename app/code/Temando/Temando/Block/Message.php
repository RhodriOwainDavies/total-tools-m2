<?php

namespace Temando\Temando\Block;

use Temando\Temando\Model\Origin;

class Message extends AbstractBlock
{
    
    /**
    * Message Template.
    *
    * @var string
    */
    protected $_template = 'Temando_Temando::message.phtml';

    /**
     * Origin Collection.
     *
     * @var \Temando\Temando\Model\Resource\Origin\Collection
     */
    protected $_originCollection;

    /**
    * Checkout Cart Model.
    *
    * @var \Magento\Checkout\Model\Cart
    */
    protected $_cart;

    /**
    * Catalog ProductFactory Model.
    *
    * @var \Magento\Catalog\Model\ProductFactory
    */
    protected $_productFactory;
    
    /**
    * Customer Session.
    *
    * @var \Magento\Customer\Model\Session
    */
    protected $_customerSession;
    
    /**
    * Current Customer Session Address.
    *
    * @var \Magento\Customer\Helper\Session\CurrentCustomerAddress
    */
    protected $_currentCustomerAddress;
    
    /**
    * Logger.
    *
    * @var \Psr\Log\LoggerInterface
    */
    protected $_logger;
    
    /**
    * Shipping Address Array.
    *
    * @var array
    */
    protected $_shippingAddress;
    
    /**
    * Shipping Additional Message Array.
    *
    * @var array
    */
    protected $_message;

    /**
     * Message constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Temando\Temando\Model\Resource\Origin\Collection $originCollection
     * @param \Magento\Checkout\Model\Cart
     * @param \Magento\Catalog\Model\ProductFactory
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Customer\Helper\Session\CurrentCustomerAddress
     */
    public function __construct(
        \Temando\Temando\Block\Context $context,
        \Temando\Temando\Model\Resource\Origin\Collection $originCollection,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Helper\Session\CurrentCustomerAddress $currentCustomerAddress,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_originCollection = $originCollection;
        $this->_cart = $cart;
        $this->_productFactory = $productFactory;
        $this->_customerSession = $customerSession;
        $this->_currentCustomerAddress = $currentCustomerAddress;
        $this->_logger = $context->getLogger();
    }
    
    /**
    * Prepare shipping additional message
    *
    * @return array
    */
    public function getShippingAdditionalMessage()
    {
        $items = $this->_cart->getQuote()->getAllItems();
        $message = array(
            'error'     => array(
                'type'      => 'error',
                'message'   => array()
            ),
            'notice'    => array(
                'type'      => 'notice',
                'message'   => array()
            ),
            'success'   => array(
                'type'      => 'success',
                'message'   => array()
            )
        );
        $skus = array();
        $isStockCheckRequired = true;
        $isDangerous = false;
        foreach ($items as $item) {
            $_product = $this->_productFactory->create();
            $_product = $_product->loadByAttribute('sku', $item->getSku());
            $stockAvailabilityCode = $_product->getAttributeText('stock_availability_code');
            //check if any product is not flagged as OD
            if ($stockAvailabilityCode != Origin::STOCK_ON_DEMAND) {
                $isStockCheckRequired = false;
            }
            //check if any product is flagged as dangerous
            $shippingDangerous = $_product->getData('shipping_dangerous');
            if (isset($shippingDangerous) && ($shippingDangerous == 1)) {
                $isDangerous = true;
            }
            //build array of skus and quantites that need checking
            if ($item instanceof \Magento\Quote\Model\Quote\Item) {
                $skus[$item->getSku()] = $item->getQty();
            } elseif ($item instanceof \Magento\Sales\Model\Order\Item) {
                $skus[$item->getSku()] = $item->getQtyOrdered();
            }
        }
        if ($isDangerous) {
            $message['notice']['message'][] = __(Origin::DANGEROUS_ERROR_MESSAGE);
        } elseif ($isStockCheckRequired) {
            if (isset($this->_shippingAddress) && isset($this->_shippingAddress['postcode'])) {
                $postcode = $this->_shippingAddress['postcode'];
            } elseif ($this->_customerSession->isLoggedIn()) {
                $shippingAddress = $this->_currentCustomerAddress->getDefaultShippingAddress();
                $postcode = $shippingAddress ? $shippingAddress->getPostcode() : null;
            }
            if (isset($postcode)) {
                $origin = $this->_originCollection->getOriginByInventory($items, $postcode);
                if (!$origin->hasStock($skus)) {
                    $message['notice']['message'][] = __(Origin::ON_DEMAND_ERROR_MESSAGE);
                }
            }
        }
        $this->setShippingAdditionalMessage($message);

        return $this->_message;
    }

    /**
    * Set shipping address
    *
    * @param array $shippingAddress
    *
    * @return $this
    */
    public function setShippingAddress($shippingAddress)
    {
        $this->_shippingAddress = $shippingAddress;

        return $this;
    }
    
    /**
    * Get shipping address
    *
    * @return array
    */
    public function getShippingAddress()
    {
        return $this->_shippingAddress;
    }
    
    /**
    * Set shipping additional message
    *
    * @param array $message
    *
    * @return array
    */
    public function setShippingAdditionalMessage($message)
    {
        if (is_array($message)) {
            $this->_message = $message;
        }

        return $this;
    }
}
