<?php

namespace Temando\Temando\Controller\Adminhtml\Checkout;

class DisableDate extends \Magento\Framework\App\Action\Action
{
    /**
     * ResultJsonFactory.
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Origin Collection.
     *
     * @var \Magestore\Storepickup\Model\StoreFactory
     */
    protected $_originCollection;

    /**
     * DisableDate constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Temando\Temando\Model\OriginFactory $originCollection
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Temando\Temando\Model\OriginFactory $originCollection
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_originCollection = $originCollection;
        parent::__construct($context);
    }

    public function execute()
    {
        $date = array();
        $closed = array();
        $holiday_date = array();
        $storeId = $this->getRequest()->getParam('store_id');
        $collectionstore = $this->_originCollection->create();
        $store = $collectionstore->load($storeId, 'origin_id');
        $holidaysdata = $store->getHolidaysData('');
        foreach ($holidaysdata as $holidays) {
            foreach ($holidays['date'] as $_date) {
                $holiday_date[]=date("m/d/Y", strtotime($_date));
            }
        }
        if (!$store->isOpenday('monday')) {
            $closed[] = 1;
        }
        if (!$store->isOpenday('tuesday')) {
            $closed[] = 2;
        }
        if (!$store->isOpenday('wednesday')) {
            $closed[] = 3;
        }
        if (!$store->isOpenday('thursday')) {
            $closed[] = 4;
        }
        if (!$store->isOpenday('friday')) {
            $closed[] = 5;
        }
        if (!$store->isOpenday('saturday')) {
            $closed[] = 6;
        }
        if (!$store->isOpenday('sunday')) {
            $closed[] = 0;
        }
        $date['holiday'] = $holiday_date;
        $date['schedule'] = $closed;

        return $this->getResponse()->setBody(\Zend_Json::encode($date));
    }
}
