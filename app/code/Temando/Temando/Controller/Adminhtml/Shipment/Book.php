<?php
namespace Temando\Temando\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Book extends \Magento\Backend\App\Action
{
    /**
     * Object Manager.
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * Custom Temando Logger.
     *
     * @var \Temando\Temando\Logger\General\Logger
     */
    protected $_logger;

    /**
     * Temando Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    protected $_shipmentSender;
    /**
     * Constructor.
     *
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender,
        \Temando\Temando\Logger\General\Logger $logger
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_objectManager = $objectManager;
        $this->_shipmentSender = $shipmentSender;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Temando_Temando::temando_shipments_process');
    }

    /**
     * Save action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->_helper = $this->_objectManager->create('Temando\Temando\Helper\Data');
        $resultRedirect = $this->resultRedirectFactory->create();
        $shipment = $this->_objectManager->create('Temando\Temando\Model\Shipment');

        $shipment_id = $this->getRequest()->getParam('shipment');
        if ($shipment_id) {
            $shipment->load($shipment_id);
        }

        if (!$this->_helper->checkShipmentPermission($shipment)) {
            $this->messageManager->addErrorMessage(__('You do not have permission to book this shipment'));
            return $resultRedirect->setPath('*/*/edit', array('shipment_id' => $shipment_id));
        }

        if (!$shipment->getOrder()->canShip()) {
            $this->messageManager->addErrorMessage(
                __('Order # %s cannot be shipped.', $shipment->getOrderNumber())
            );
            return $resultRedirect->setPath('*/*/view', array('shipment_id' => $shipment_id));
        }
        $quote_id = $this->getRequest()->getParam('quote');

        /* @var $quote \Temando\Temando\Model\Quote */
        $quote = $this->_objectManager->create('Temando\Temando\Model\Quote');
        $quote->load($quote_id);
        $error = null;
        if (!$shipment->getId()) {
             $this->messageManager->addErrorMessage(__('Shipment does not exist'));
        } else {
            if (!$quote->getData('quote_id')) {
                $error = __(
                    'Selected quote is no longer available. '
                    . 'Please refresh quotes by saving the shipment and try again'
                );
            } else {
                if (!$error) {
                    try {
                        $booking_result = $this->_makeBooking($shipment, $quote);

                        if ($booking_result instanceof \SoapFault) {
                            $errorMessage = __($booking_result->getCode()).' '.__($booking_result->getMessage());
                            $this->messageManager->addErrorMessage($errorMessage);
                            return $resultRedirect->setPath('*/*/view', array('shipment_id' => $shipment_id));
                        }
                    } catch (Exception $e) {
                        $this->_logger->debug('Book.php makeBooking exception');
                        $this->_logger->debug($e->getMessage());
                        $error = $e->getMessage();
                    }
                }
            }
        }

        if (!$error && $booking_result) {
            try {
                $shipment->processBookingResult($booking_result, $quote);
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        if ($error) {
            $this->messageManager->addErrorMessage(__($error));
        } else {
            $this->messageManager->addSuccessMessage(__('Shipment booked'));
        }
        //$shipment->releaseLock();
        $order = $shipment->getOrder();
        $shipmentCollection = $order->getShipmentsCollection();
        foreach ($shipmentCollection as $shipment) {
            $this->_shipmentSender->send($shipment);
        }
        return $resultRedirect->setPath('*/*/view', array('shipment_id' => $shipment_id));
    }

    /**
     * Make booking call
     *
     * @param \Temando\Temando\Model\Shipment $shipment
     * @param \Temando\Temando\Model\Quote $quote
     */
    protected function _makeBooking(\Temando\Temando\Model\Shipment $shipment, \Temando\Temando\Model\Quote $quote)
    {
        $scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');

        /* @var $order \Magento\Sales\Model\Order */
        $order = $shipment->getOrder();
        $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
            ->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING)
            ->save();

        /* hide ship button in Sale_Order_View */
        foreach ($order->getAllItems() as $item) {
            $item->setQtyShipped($item->getQtyOrdered())
                ->save();
        }
            
        /* @var $origin \Temando\Temando\Model\Origin */
        $origin = $this->_objectManager->create('Temando\Temando\Model\Origin');
        $origin->load($shipment->getOriginId());
        /* @var $request \Temando\Temando\Model\Api\Request */
        $request = $this->_objectManager->create('Temando\Temando\Model\Api\Request');
        $request
            ->setMagentoQuoteId($order->getQuoteId())
            ->setConnectionParams($origin->getTemandoProfile())
            ->setDestination(
                $shipment->getDestinationCountry(),
                $shipment->getDestinationPostcode(),
                $shipment->getDestinationCity(),
                $shipment->getDestinationStreet(),
                $shipment->getDestinationType()
            )
            ->setOrigin($origin->getName(), $origin->getCountry())
            ->setArticles($shipment->getArticles(true))
            ->setGoodsCurrency($order->getStore()->getCurrentCurrencyCode())
            ->setDeliveryOptions($shipment->getDeliveryOptions())
            ->setItems($shipment->getBoxes()->getItems());

        $readyDate = $this->_helper->getReadyDate();
        $request->setReady($readyDate);
        $request_array = $request->toRequestArray();
        $request_array['origin'] = array(
            'description' => $origin->getName()
        );
        $request_array['destination'] = array(
            'contactName' => $shipment->getDestinationContactName(),
            'companyName' => $shipment->getDestinationCompanyName(),
            'street' => $shipment->getDestinationStreet(),
            'suburb' => $shipment->getDestinationCity(),
            'city' => $shipment->getDestinationRegion(),
            'code' => $shipment->getDestinationPostcode(),
            'country' => $shipment->getDestinationCountry(),
            'phone1' => preg_replace('/\D/', '', $shipment->getDestinationPhone()),
            'phone2' => '',
            'fax' => '',
            'email' => $shipment->getDestinationEmail(),
        );

        $request_array['quote'] = $quote->toBookingRequestArray();
        $request_array['payment'] = array(
            'paymentType' => 'Account',
        );

        $labelType = $scopeConfig->getValue(
            'temando/options/label_type',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($labelType) {
            $request_array['labelPrinterType'] = $labelType;
        }
        $request_array['reference'] = $order->getIncrementId();

        /* @var $api /Temando/Temando/Model/Api/Client */
        $api = $this->_objectManager->create('\Temando\Temando\Model\Api\Client');
        $api->connect($origin->getTemandoProfile());
        return $api->makeBooking($request_array);
    }
}
