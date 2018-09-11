<?php

namespace Temando\Temando\Controller\Checkout;

class GetAdditionalMessage extends \Magento\Framework\App\Action\Action
{
    /**
    * Message Block.
    *
    * @var \Temando\Temando\Block\Message
    */
    protected $_message;

    /**
     * GetAdditionalMessage constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Temando\Temando\Block\Message $message
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Temando\Temando\Block\Message $message
    ) {
        parent::__construct($context);
        $this->_message = $message;
    }

    public function execute()
    {
        $shippingAddressJson = $this->getRequest()->getParam('shippingAddress');
        $shippingAddress = json_decode($shippingAddressJson, true);
        $additionalMessage = $this->_message->setShippingAddress($shippingAddress)->getShippingAdditionalMessage();
        return $this->getResponse()->setBody(\Zend_Json::encode($additionalMessage));
    }
}
