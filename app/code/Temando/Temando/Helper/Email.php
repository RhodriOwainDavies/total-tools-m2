<?php

namespace Temando\Temando\Helper;

use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

/**
 * Helper Data.
 */

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    //const XML_PATH_ADMIN_EMAIL_IDENTITY = "trans_email/ident_general";
    //const XML_PATH_ADMIN_EMAIL_GENERAL_NAME = 'trans_email/ident_general/name';
    const XML_PATH_SALES_EMAIL_IDENTITY = "trans_email/ident_sales/email";
    const XML_PATH_SALES_EMAIL_IDENTITY_NAME = "trans_email/ident_sales/name";
    const TEMPLATE_ID_NONE_EMAIL = 'none_email';

    const XML_PATH_ORDER_ALLOCATED_EMAIL = 'temando/notify_merchant_new_order/template';
    const XML_PATH_PICKUP_READY_EMAIL = 'temando/notify_customer_pickup_ready/template';

    protected $_logger;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Payment\Helper\Data $paymentHelperData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magestore\Storepickup\Helper\Data $storePickupHelper
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_transportBuilder = $transportBuilder;
        $this->_paymentHelperData = $paymentHelperData;
        $this->_addressRenderer = $addressRenderer;
        $this->_objectManager = $objectManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_priceCurrency = $priceCurrency;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_storePickupHelper = $storePickupHelper;
        $this->_logger = $context->getLogger();
    }

    protected function getPaymentHtml(Order $order, $storeId)
    {
        return $this->_paymentHelperData->getInfoBlockHtml(
            $order->getPayment(),
            $storeId
        );
    }

    protected function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->_addressRenderer->format($order->getShippingAddress(), 'html');
    }

    protected function getFormattedBillingAddress($order)
    {
        return $this->_addressRenderer->format($order->getBillingAddress(), 'html');
    }

    public function getConfig($path)
    {
        $storeId = $this->_storeManager->getStore()->getId();

        return $this->_scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }


    public function sendOrderAllocationEmailToMerchant(
        \Magento\Sales\Model\Order $order,
        \Temando\Temando\Model\Origin $origin
    ) {
        //$origin = $shipment->getOrigin();
        $storeId = $order->getStore()->getId();
        $this->inlineTranslation->suspend();

        $template = $this->getConfig(self::XML_PATH_ORDER_ALLOCATED_EMAIL);
        if ($template === self::TEMPLATE_ID_NONE_EMAIL) {
            return ;
        }

        try {
            $transport = $this->_transportBuilder->setTemplateIdentifier(
                $template
            )->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ]
            )->setTemplateVars(
                [
                    'order' => $order,
                    'billing' => $order->getBillingAddress(),
                    'payment_html' => $this->getPaymentHtml($order, $storeId),
                    'store' => $order->getStore(),
                    'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
                    'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
                    'origin' => $origin
                ]
            )->setFrom(
                [
                    'email' => $this->_scopeConfig->getValue(
                        self::XML_PATH_SALES_EMAIL_IDENTITY,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $storeId
                    ),
                    'name' => $this->_scopeConfig->getValue(
                        self::XML_PATH_SALES_EMAIL_IDENTITY_NAME,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $storeId
                    ),
                ]
            )->addTo(
                $origin->getContactEmail(),
                $origin->getContactName()
            )->getTransport();
            $transport->sendMessage();
        } catch (\Magento\Framework\Exception\MailException $ex) {
            $this->_logger->debug($ex->getMessage());
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
        $this->inlineTranslation->resume();

        return $this;
    }

    public function sendPickupReadyEmailToCustomer($pickup)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $origin = $pickup->getOrigin();
        $order = $pickup->getOrder();

        $template = $this->getConfig(self::XML_PATH_PICKUP_READY_EMAIL);
        if ($template === self::TEMPLATE_ID_NONE_EMAIL) {
            return ;
        }

        $this->inlineTranslation->suspend();
        //$information_sender = $this->_messageFactory->create()->load($id_message);
        $email_sender = $origin->getContactEmail();
        $name_sender = $origin->getContactName();


        $mailSubject = "Your Order #" . $order->getIncrementId() . " is ready for collection";
        $sender = array(
            'name' => $name_sender,
            'email' => $email_sender,
        );
        //$message = $information_sender->getMessage();
//        $sendTo = array(
//            $this->getConfig(self::XML_PATH_ADMIN_EMAIL_IDENTITY),
//        );

//        $sendToEmail = $pickup->getBillingEmail();
//        $sendToName = $pickup->getBillingContactName();

        //foreach ($sendTo as $item) {
        $email_contact = $pickup->getBillingEmail();
        $name_contact = $pickup->getBillingContactName();
        try {
            $transport = $this->_transportBuilder->setTemplateIdentifier(
                $template
            )->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ]
            )->setTemplateVars(
                [
                    'order' => $order,
                    'billing' => $order->getBillingAddress(),
                    'payment_html' => $this->getPaymentHtml($order, $storeId),
                    'store' => $order->getStore(),
                    'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
                    'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
                    'pickup' => $pickup,
                    'origin' => $origin
                ]
            )->setFrom(
                [
                    'email' => $origin->getContactEmail(),
                    'name' => $origin->getContactName()
                ]
            )->addTo(
                $email_contact,
                $name_contact
            )->getTransport();
            $transport->sendMessage();
        } catch (\Magento\Framework\Exception\MailException $ex) {
            $this->_logger->debug($ex->getMessage());
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
        //}
        $this->inlineTranslation->resume();
    }
}
