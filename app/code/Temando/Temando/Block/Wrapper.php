<?php

namespace Temando\Temando\Block;

class Wrapper extends \Temando\Temando\Block\AbstractBlock
{
    protected $_originCollection;
    protected $_template = 'Temando_Temando::wrapper.phtml';
    protected $_helper;
    protected $_cart;
 
    public function __construct(
        \Temando\Temando\Block\Context $context,
        \Temando\Temando\Model\ResourceModel\Origin\Collection $originCollection,
        \Temando\Temando\Helper\Data $helper,
        \Magento\Checkout\Model\Cart $cart,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_originCollection = $originCollection;
        $this->_helper = $helper;
        $this->_cart = $cart;
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    
    public function getListStore()
    {
        $collection = $this->_originCollection;
        $origins = $collection->addFieldToFilter('is_active', '1')
            ->addFieldToFilter('allow_store_collection', '1')
            ->addFieldToSelect(['origin_id', 'name','street','contact_phone_1','latitude','longitude', 'erp_id'])
            ->addOrder('name', 'ASC')
            ->getData();

        // get all items in cart and their quantity
        $items = $this->_cart->getQuote()->getAllItems();
        $products = array();
        foreach ($items as $item) {
            $products[$item->getSku()] = $item->getQty();
        }

        // update origins with stock level message
        $originsStockLevelMessages = $this->_helper->getOriginsStockLevelMessage($origins, $products);
        return \Zend_Json::encode($originsStockLevelMessages);
    }
}
