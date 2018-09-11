<?php

namespace Temando\Temando\Controller\Adminhtml\Checkout;

class ChangeStore extends \Magento\Framework\App\Action\Action
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
     * ChangeStore constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Backend\Model\Session $backendSession
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
        $storepickup_session = array('store_id' => $this->getRequest()->getParam('store_id'));
        $this->_backendSession->setData('storepickup', $storepickup_session);
        return $this->getResponse()->setBody(\Zend_Json::encode($storepickup_session));
    }
}
