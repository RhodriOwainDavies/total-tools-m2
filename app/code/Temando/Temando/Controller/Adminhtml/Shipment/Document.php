<?php
namespace Temando\Temando\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Document extends \Magento\Backend\App\Action
{
    /**
     * Object Manager.
     *
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $_objectManager;

    /**
     * Result Page Factory.
     *
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Temando Helper.
     *
     * @var /Temando/Temando/Helper/Data
     */
    protected $_helper;

    /**
     * Constructor.
     *
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_objectManager = $objectManager;
        parent::__construct($context);
        $this->resultPageFactory = $context->getResultFactory();
        $this->_resultRawFactory = $this->_objectManager->create('\Magento\Framework\Controller\Result\RawFactory');
        $this->_helper = $this->_objectManager->create('\Temando\Temando\Helper\Data');
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $booking_id = $this->getRequest()->getParam('booking');
        $document = $this->getRequest()->getParam('document');

        $booking = $this->_objectManager->create('Temando\Temando\Model\Booking');
        $booking->load($booking_id);

        $shipment = $booking->getShipment();
        if (!$this->_helper->checkShipmentPermission($shipment)) {
            $this->messageManager->addErrorMessage(
                __(
                    'You do not have permission to view this shipment (ID : '.$shipment->getId().')'
                )
            );
            return $resultRedirect->setPath('*/*');
        }

        $output = null;
        switch ($document) {
            default:
                $output = $booking->getLabelDocument();
                break;
            case 'consignment':
                $output = $booking->getConsignmentDocument();
                break;
        }

        $output = trim($output);
        $filename = $document . '-' . $shipment->getId(). '-' . $booking_id.'.pdf';
        //echo $filename . "<br/>" . $output;exit;
        return $this->_helper->_prepareDownloadResponse($filename, $output);
    }

    /**
     * Is the user allowed to view the shipment.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Temando_Temando::temando_shipments_view');
    }
}
