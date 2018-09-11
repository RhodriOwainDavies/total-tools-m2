<?php

namespace Temando\Temando\Controller\Adminhtml\Checkout;

class ChangeDate extends \Magento\Framework\App\Action\Action
{
    /**
     * Result JSON Factory.
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Backend Session.
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * Store Factory.
     *
     * @var \Temando\Temando\Model\StoreFactory
     */
    protected $_storeCollection;

    /**
     * Format Date.
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_formatdate;

    /**
     * Temando Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_storepickupHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Temando\Temando\Model\OriginFactory $originCollection,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $gmtdate,
        \Temando\Temando\Helper\Data $storepickupHelper,
        \Magento\Backend\Model\Session $backendSession
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_originCollection = $originCollection;
        $this->_backendSession = $backendSession;
        $this->_storepickupHelper = $storepickupHelper;
        $this->_formatdate = $gmtdate;
        parent::__construct($context);
    }

    public function execute()
    {
        $date = array();
        $date['error']= 0;
        $today = date("Y-m-d");
        $thisTime = date("H:i");
        $storeId = $this->getRequest()->getParam('store_id');
        $shippingDateString = $this->getRequest()->getParam('shipping_date');
        $shippingDate = date('Y-m-d', strtotime($shippingDateString));
        $dayofweek= strtolower(date('l', strtotime($shippingDate)));
        $collectionstore = $this->_originCollection->create();
        $store = $collectionstore->load($storeId, 'origin_id');
        $storeSchedule = $store->getSchedule();
        $hasBreakTime= $store->hasBreakTime($dayofweek);
        // check special days
        $specialsData = $store->getSpecialdaysData();
        $specialday = false;
        foreach ($specialsData as $specialID) {
            $isSpecialday = array_search($shippingDate, $specialID['date'], false);
            if ($isSpecialday) {
                $specialday= true;
                $date['time_open']= $specialID['time_open'];
                $date['time_close']= $specialID['time_close'];
            }
        }
        // if shipping date is today
        if ($shippingDate==$today) {
            if ($specialday !== false) {
                $date['html']= $this->_storepickupHelper->generateTimes(
                    $date['time_open'],
                    $date['time_close'],
                    $thisTime
                );
                return $this->getResponse()->setBody(\Zend_Json::encode($date));
            } else {
                $date['time_open'] = $storeSchedule->getData($dayofweek . '_open');
                $date['time_close'] = $storeSchedule->getData($dayofweek . '_close');
                if ($thisTime>$date['time_close']) {
                    $date['error']= __('The worktime has been finished. Please select an other day');
                    return $this->getResponse()->setBody(\Zend_Json::encode($date));
                }
                if (!$hasBreakTime) {
                    $date['html'] = $this->_storepickupHelper->generateTimes(
                        $date['time_open'],
                        $date['time_close'],
                        $thisTime
                    );
                } else {
                    $date['open_break'] = $storeSchedule->getData($dayofweek . '_open_break');
                    $date['html'] = $this->_storepickupHelper->generateTimes(
                        $date['time_open'],
                        $date['open_break'],
                        $thisTime
                    );
                    //var_dump($thisTime);
                    $date['close_break'] = $storeSchedule->getData($dayofweek . '_close_break');
                    $date['html'] .= $this->_storepickupHelper->generateTimes(
                        $date['close_break'],
                        $date['time_close'],
                        $thisTime
                    );
                }
                return $this->getResponse()->setBody(\Zend_Json::encode($date));
            }
        }
        // shipping date is a specialday
        if ($specialday) {
            $date['html']= $this->_storepickupHelper->generateTimes($date['time_open'], $date['time_close']);
            return $this->getResponse()->setBody(\Zend_Json::encode($date));
        }
        //shipping date is a normal day
        if (!$hasBreakTime) {
            $date['time_open'] = $storeSchedule->getData($dayofweek . '_open');
            $date['time_close'] = $storeSchedule->getData($dayofweek . '_close');
            $date['html'] = $this->_storepickupHelper->generateTimes($date['time_open'], $date['time_close']);
        } else {
            $date['time_open'] = $storeSchedule->getData($dayofweek . '_open');
            $date['open_break'] = $storeSchedule->getData($dayofweek . '_open_break');
            $date['html'] = $this->_storepickupHelper->generateTimes($date['time_open'], $date['open_break']);
            $date['close_break'] = $storeSchedule->getData($dayofweek . '_close_break');
            $date['time_close'] = $storeSchedule->getData($dayofweek . '_close');
            $date['html'] .= $this->_storepickupHelper->generateTimes($date['close_break'], $date['time_close']);
        }
        return $this->getResponse()->setBody(\Zend_Json::encode($date));
    }
}
