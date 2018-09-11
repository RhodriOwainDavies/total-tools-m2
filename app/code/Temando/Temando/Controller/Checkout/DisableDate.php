<?php

namespace Temando\Temando\Controller\Checkout;

class DisableDate extends \Magento\Framework\App\Action\Action
{
    /**
     * JSON Factory.
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Checkout Session.
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    
    protected $_originCollection;
    
    /**
     * Format Date.
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_formatdate;

    protected $_helper;

    protected $_cart;

    /**
     * DisableDate constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magestore\Storepickup\Model\StoreFactory $storeCollection
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $gmtdate
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Model\Cart $cart
     */

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Temando\Temando\Model\Origin $originCollection,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $gmtdate,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Temando\Temando\Helper\Data $helper,
        \Magento\Checkout\Model\Cart $cart
    ) {

        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_originCollection = $originCollection;
        $this->_checkoutSession = $checkoutSession;
        $this->_formatdate = $gmtdate;
        $this->_helper = $helper;
        $this->_cart = $cart;
        parent::__construct($context);
    }
    public function execute()
    {
        $date = array();
        $closed = array();
        $holiday_date = array();
        $storeId = $this->getRequest()->getParam('origin_id');
        $store = $this->_originCollection->load($storeId);

        // get all items in cart and their quantity
        $items = $this->_cart->getQuote()->getAllItems();
        $products = array();
        foreach ($items as $item) {
            $products[$item->getSku()] = $item->getQty();
        }

        // update store with stock level
        $originsStockLevelMessages = $this->_helper->getOriginsStockLevelMessage(
            array($this->_originCollection->load($storeId)->getData()),
            $products
        );
        $stockMessage = $originsStockLevelMessages[0]['stock_level_message'];
        $holidaysdata = $store->getHolidaysData($stockMessage);
        
        if ($holidaysdata != '') {
            foreach ($holidaysdata as $holidays) {
                foreach ($holidays['date'] as $_date) {
                    $holiday_date[]=date("m/d/Y", strtotime($_date));
                }
            }
        }
        if (!$store->isOpenday('monday')) {
            $closed[]=1;
        }
        if (!$store->isOpenday('tuesday')) {
            $closed[]=2;
        }
        if (!$store->isOpenday('wednesday')) {
            $closed[]=3;
        }
        if (!$store->isOpenday('thursday')) {
            $closed[]=4;
        }
        if (!$store->isOpenday('friday')) {
            $closed[]=5;
        }
        if (!$store->isOpenday('saturday')) {
            $closed[]=6;
        }
        if (!$store->isOpenday('sunday')) {
            $closed[]=0;
        }
        $date['holiday'] = $holiday_date;
        $date['schedule'] = $closed;

        return $this->getResponse()->setBody(\Zend_Json::encode($date));
    }
}
