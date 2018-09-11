<?php

namespace Temando\Temando\Controller\Checkout;

class ChangeTime extends \Magento\Framework\App\Action\Action
{
    /**
     * JSON Factory.
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * CheckShipping constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    protected $_checkoutSession;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_checkoutSession = $checkoutSession;
    }
    public function execute()
    {
        $storepickup_session = $this->_checkoutSession->getData('storepickup_session');
        $storepickup_session['origin_id'] = $this->getRequest()->getParam('origin_id');
        $storepickup_session['shipping_date'] = $this->getRequest()->getParam('shipping_date');
        $storepickup_session['shipping_time'] = $this->getRequest()->getParam('shipping_time');
        $this->_checkoutSession->setData('storepickup_session', $storepickup_session);
        return $this->getResponse()->setBody(\Zend_Json::encode($storepickup_session));
    }
}
