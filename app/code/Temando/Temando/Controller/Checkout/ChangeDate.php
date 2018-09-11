<?php

namespace Temando\Temando\Controller\Checkout;

class ChangeDate extends \Magento\Framework\App\Action\Action
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

    /**
     * Store Pickup Helper.
     *
     * @var \Magestore\Storepickup\Helper\Data
     */
    protected $_storepickupHelper;

    /**
     * ChangeDate constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $gmtdate
     * @param \Magestore\Storepickup\Helper\Data $storepickupHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $gmtdate,
        \Magestore\Storepickup\Helper\Data $storepickupHelper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_resultJsonFactory = $resultJsonFactory;
      //  $this->_storeCollection = $storeCollection;
        $this->_originCollection = $objectManager->create('Temando\Temando\Model\Origin');
        $this->_checkoutSession = $checkoutSession;
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
        $storeId = $this->getRequest()->getParam('origin_id');
        $shippingDateString = $this->getRequest()->getParam('shipping_date');
        $shippingDate = date('Y-m-d', strtotime($shippingDateString));
        $dayofweek= strtolower(date('l', strtotime($shippingDate)));
        // save pickup date to data
        $store = $this->_originCollection->load($storeId);
        $hasBreakTime= $store->hasBreakTime($dayofweek);
        $specialday = false;

        /* check special days */
        $specialsData = $store->getSpecialdaysData();
        if ($specialsData != '') {
            foreach ($specialsData as $specialID) {
                $isSpecialday = array_search($shippingDate, $specialID['date'], false);
                if ($isSpecialday !== false) {
                    $specialday = true;
                    $date['time_open']= $specialID['time_open'];
                    $date['time_close']= $specialID['time_close'];
                }
            }
        }
        // if shipping date is today
        if ($shippingDate==$today) {
            if ($specialday) {
                //today is a special day
                $date['html'] = $this->_storepickupHelper->generateTimes(
                    $date['time_open'],
                    $date['time_close'],
                    $thisTime
                );
                return $this->getResponse()->setBody(\Zend_Json::encode($date));
            } else {
                 //today is a normal day
                $date['time_open'] = $store->getSchedule()->getData($dayofweek . '_open');
                $date['time_close'] = $store->getSchedule()->getData($dayofweek . '_close');

                //The worktime has been finished
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
                    $date['open_break'] = $store->getSchedule()->getData($dayofweek . '_open_break');
                    $date['html'] = $this->_storepickupHelper->generateTimes(
                        $date['time_open'],
                        $date['open_break'],
                        $thisTime
                    );
                    //var_dump($thisTime);
                    $date['close_break'] = $store->getSchedule()->getData($dayofweek . '_close_break');
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
            $date['time_open'] = $store->getSchedule()->getData($dayofweek . '_open');
            $date['time_close'] = $store->getSchedule()->getData($dayofweek . '_close');
            $date['html'] = $this->_storepickupHelper->generateTimes($date['time_open'], $date['time_close']);
        } else {
            $date['time_open'] = $store->getSchedule()->getData($dayofweek . '_open');
            $date['open_break'] = $store->getSchedule()->getData($dayofweek . '_open_break');
            $date['html'] = $this->_storepickupHelper->generateTimes($date['time_open'], $date['open_break']);
            $date['close_break'] = $store->getSchedule()->getData($dayofweek . '_close_break');
            $date['time_close'] = $store->getSchedule()->getData($dayofweek . '_close');
            $date['html'] .= $this->_storepickupHelper->generateTimes($date['close_break'], $date['time_close']);
        }
        return $this->getResponse()->setBody(\Zend_Json::encode($date));
    }
}
