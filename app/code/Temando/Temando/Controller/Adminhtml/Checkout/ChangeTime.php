<?php

namespace Temando\Temando\Controller\Adminhtml\Checkout;

class ChangeTime extends \Magento\Framework\App\Action\Action
{
    /**
     * Result JSON Factory.
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Checkout Session.
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_backendSession;

    /**
     * ChangeTime constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Checkout\Model\Session $backendSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Backend\Model\Session $backendSession
    ) {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_backendSession = $backendSession;
    }

    public function execute()
    {
        $storepickup_session = $this->_backendSession->getData('storepickup');
        $storepickup_session['store_id'] = $this->getRequest()->getParam('store_id');
        $storepickup_session['shipping_date'] = $this->getRequest()->getParam('shipping_date');
        $storepickup_session['shipping_time'] = $this->getRequest()->getParam('shipping_time');
        $this->_backendSession->setData('storepickup', $storepickup_session);
        return $this->getResponse()->setBody(\Zend_Json::encode($storepickup_session));
    }
}
